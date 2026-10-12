const blogs = require('./blogs.json');
const blogCategories = [{slug:'all',name:'Tüm Yazılar'}, ...Array.from(new Map(blogs.map(b => [b.categorySlug, { slug: b.categorySlug, name: b.category }])).values())];

const blogRepository = {
  all() {
    return blogs;
  },
  featured() {
    return blogs.find(b => b.featured) || blogs[0];
  },
  bySlug(slug) {
    return blogs.find(b => b.slug === slug || b.legacy_slug === slug) || null;
  },
  byCategory(categorySlug) {
    if (!categorySlug || categorySlug === 'all') return blogs;
    return blogs.filter(b => b.categorySlug === categorySlug);
  },
  related(currentId, limit = 3) {
    return blogs.filter(b => b.id !== currentId).slice(0, limit);
  },
  categories() {
    return blogCategories;
  }
};

module.exports = {
  blogs,
  blogCategories,
  blogRepository
};
