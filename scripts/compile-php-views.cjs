// One-way, build-time conversion of the existing EJS markup. No Node.js on hosting.
// Unsupported syntax fails the build instead of producing an approximate template.
const fs = require('node:fs');
const path = require('node:path');
const acorn = require('acorn');
const root = path.resolve(__dirname, '..');
const quote = value => "'" + String(value).replace(/\\/g, '\\\\').replace(/'/g, "\\'") + "'";
const str = n => n.type === 'Literal' && typeof n.value === 'string' || n.type === 'TemplateLiteral' || n.type === 'BinaryExpression' && n.operator === '+' && (str(n.left) || str(n.right));
const block = n => n.type === 'BlockStatement' ? n.body.map(stmt).join('') : stmt(n);
function expr(n) {
  if (!n) return 'null';
  if (n.type === 'Literal') {
    if (n.regex) return quote('~' + n.regex.pattern.replace(/~/g, '\\~') + '~u');
    return typeof n.value === 'string' ? quote(n.value) : JSON.stringify(n.value);
  }
  if (n.type === 'Identifier') return n.name === 'undefined' ? 'null' : '$' + n.name;
  if (n.type === 'ArrayExpression') return '[' + n.elements.map(expr).join(', ') + ']';
  if (n.type === 'ObjectExpression') return '[' + n.properties.map(p => quote(p.key.name || p.key.value) + ' => ' + expr(p.value)).join(', ') + ']';
  if (n.type === 'MemberExpression') {
    if (n.object.type === 'Identifier' && n.object.name === 'locals') return '$' + n.property.name;
    if (!n.computed && n.property.name === 'length') return 'count(' + expr(n.object) + ' ?? [])';
    return '(' + expr(n.object) + '[' + (n.computed ? expr(n.property) : quote(n.property.name)) + '] ?? null)';
  }
  if (n.type === 'LogicalExpression') return '(' + expr(n.left) + (n.operator === '||' ? ' ?: ' : ' && ') + expr(n.right) + ')';
  if (n.type === 'BinaryExpression') {
    if (n.left.type === 'UnaryExpression' && n.left.operator === 'typeof' && n.right.value === 'undefined') return (n.operator.includes('!') ? '' : '!') + 'isset(' + expr(n.left.argument) + ')';
    return '(' + expr(n.left) + ' ' + (n.operator === '+' && str(n) ? '.' : n.operator) + ' ' + expr(n.right) + ')';
  }
  if (n.type === 'UnaryExpression' && n.operator === '!') return '(!' + expr(n.argument) + ')';
  if (n.type === 'ConditionalExpression') return '(' + expr(n.test) + ' ? ' + expr(n.consequent) + ' : ' + expr(n.alternate) + ')';
  if (n.type === 'TemplateLiteral') {
    const parts = [];
    n.quasis.forEach((q,i) => { parts.push(quote(q.value.cooked)); if (n.expressions[i]) parts.push(expr(n.expressions[i])); });
    return '(' + parts.join(' . ') + ')';
  }
  if (n.type === 'ArrowFunctionExpression') {
    if (n.body.type === 'BlockStatement') throw new Error('Block callback in expression');
    return 'fn(' + n.params.map(expr).join(', ') + ') => ' + expr(n.body);
  }
  if (n.type === 'CallExpression') {
    const args = n.arguments.map(expr);
    if (n.callee.type === 'Identifier') {
      if (n.callee.name === 'include') return 'view(' + quote(n.arguments[0].value.replace('../','')) + ', ' + (args[1] || '[]') + ', get_defined_vars())';
      return n.callee.name + '(' + args.join(', ') + ')';
    }
    const obj = n.callee.object, method = n.callee.property.name;
    if (obj.name === 'Math' && method === 'ceil') return '((int) ceil(' + args[0] + '))';
    if (obj.name === 'JSON' && method === 'stringify') return 'json_encode(' + args[0] + ', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)';
    if (obj.type === 'NewExpression' && obj.callee.name === 'Date' && method === 'getFullYear') return "date('Y')";
    if (method === 'slice') return 'array_slice(' + expr(obj) + ', ' + args.join(', ') + ')';
    if (method === 'filter') return 'array_values(array_filter(' + expr(obj) + ', ' + args[0] + '))';
    if (method === 'replace') {
      // JSON_HEX_TAG already protects the serialized JSON-LD.
      if (obj.type === 'CallExpression' && obj.callee.object?.name === 'JSON') return expr(obj);
      return (n.arguments[0].regex ? 'preg_replace' : 'str_replace') + '(' + args[0] + ', ' + args[1] + ', ' + expr(obj) + ')';
    }
    throw new Error('Unsupported call: ' + method);
  }
  throw new Error('Unsupported expression: ' + n.type);
}
function stmt(n) {
  if (n.type === 'VariableDeclaration') return n.declarations.map(d => '<?php ' + expr(d.id) + ' = ' + expr(d.init) + '; ?>').join('');
  if (n.type === 'IfStatement') return '<?php if (' + expr(n.test) + '): ?>' + block(n.consequent) + (n.alternate ? '<?php else: ?>' + block(n.alternate) : '') + '<?php endif; ?>';
  if (n.type === 'ExpressionStatement') {
    const e = n.expression;
    if (e.type === 'CallExpression' && e.callee.name === '__html') return e.arguments[0].value;
    if (e.type === 'CallExpression' && ['__raw','__escaped'].includes(e.callee.name)) return '<?= ' + (e.callee.name === '__escaped' ? 'e(' + expr(e.arguments[0]) + ')' : expr(e.arguments[0])) + ' ?>';
    if (e.type === 'CallExpression' && e.callee.property?.name === 'forEach') {
      const fn = e.arguments[0];
      const as = fn.params.length === 2 ? expr(fn.params[1]) + ' => ' + expr(fn.params[0]) : expr(fn.params[0]);
      return '<?php foreach (' + expr(e.callee.object) + ' as ' + as + '): ?>' + block(fn.body) + '<?php endforeach; ?>';
    }
  }
  if (n.type === 'EmptyStatement') return '';
  throw new Error('Unsupported statement: ' + n.type);
}
function convert(source) {
  let js = '', cursor = 0;
  for (const m of source.matchAll(/<%([=-]?)([\s\S]*?)%>/g)) {
    js += '__html(' + JSON.stringify(source.slice(cursor,m.index)) + ');\n';
    js += m[1] === '=' ? '__escaped(' + m[2] + ');\n' : m[1] === '-' ? '__raw(' + m[2] + ');\n' : m[2] + '\n';
    cursor = m.index + m[0].length;
  }
  js += '__html(' + JSON.stringify(source.slice(cursor)) + ');';
  return acorn.parse(js, { ecmaVersion: 'latest' }).body.map(stmt).join('');
}
function walk(dir) {
  for (const entry of fs.readdirSync(dir,{withFileTypes:true})) {
    const input = path.join(dir,entry.name);
    if (entry.isDirectory()) { walk(input); continue; }
    if (!entry.name.endsWith('.ejs')) continue;
    const output = input.replace(path.join(root,'views'),path.join(root,'app/views')).replace(/\.ejs$/,'.php');
    try {
      const compiled = convert(fs.readFileSync(input,'utf8'));
      fs.mkdirSync(path.dirname(output),{recursive:true});
      fs.writeFileSync(output, '<?php /* Generated from ' + path.relative(root,input) + '; retain original layout. */ ?>\n' + compiled);
      console.log(path.relative(root,output));
    } catch (e) { throw new Error(input + ': ' + e.message); }
  }
}
walk(path.join(root,'views'));
