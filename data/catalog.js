const { categories, products, banners, faqs, distributorBrands, channels } = require('./catalog.json');

const catalogRepository = {
  banners() {
    return banners;
  },
  faqs() {
    return faqs;
  },
  mainCategories() {
    return categories.filter(c => !c.parent_id);
  },
  categoryBySlug(slug) {
    return categories.find(c => c.slug === slug) || null;
  },
  subCategories(parentId) {
    return categories.filter(c => Number(c.parent_id) === Number(parentId));
  },
  allProducts() {
    return [...products].sort((a, b) => Number(Boolean(b.image_path)) - Number(Boolean(a.image_path)));
  },
  featuredProducts(limit = 8) {
    return products.filter(p => p.is_featured).slice(0, limit);
  },
  productsByCategory(categoryId) {
    const childIds = this.subCategories(categoryId).map(c => c.id);
    const targetIds = [categoryId, ...childIds].map(Number);
    return this.allProducts().filter(p => targetIds.includes(Number(p.category_id)));
  },
  productBySlug(slug) {
    return products.find(p => p.slug === slug) || null;
  },
  relatedProducts(categoryId, excludeId, limit = 4) {
    return this.productsByCategory(categoryId)
      .filter(p => Number(p.id) !== Number(excludeId))
      .slice(0, limit);
  },
  distributorBrands() {
    return distributorBrands;
  },
  distributorBrandBySlug(slug) {
    return distributorBrands.find(b => b.slug === slug) || null;
  },
  search(query) {
    const needle = query.trim().toLocaleLowerCase('tr-TR');
    return products.filter(p => {
      const searchTarget = `${p.name} ${p.sku} ${p.description || ''}`.toLocaleLowerCase('tr-TR');
      return searchTarget.includes(needle);
    });
  }
};

module.exports = {
  categories,
  products,
  banners,
  faqs,
  distributorBrands,
  channels,
  catalogRepository
};

