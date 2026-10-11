<?php

declare(strict_types=1);

final class CatalogRepository
{
    public function __construct(private ?PDO $pdo, private array $demo) {}

    public function banners(): array
    {
        if (!$this->pdo) return $this->demo['banners'];
        return $this->pdo->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY sort_order, id")->fetchAll();
    }

    public function mainCategories(): array
    {
        if (!$this->pdo) return array_values(array_filter($this->demo['categories'], fn($item) => empty($item['parent_id'])));
        return $this->pdo->query("SELECT * FROM categories WHERE parent_id IS NULL AND is_active = 1 ORDER BY sort_order, name")->fetchAll();
    }

    public function categoryBySlug(string $slug): ?array
    {
        if (!$this->pdo) {
            foreach ($this->demo['categories'] as $item) if ($item['slug'] === $slug) return $item;
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE slug = :slug AND is_active = 1 LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function subCategories(int $parentId): array
    {
        if (!$this->pdo) return array_values(array_filter($this->demo['categories'], fn($item) => (int)($item['parent_id'] ?? 0) === $parentId));
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE parent_id = :id AND is_active = 1 ORDER BY sort_order, name");
        $stmt->execute(['id' => $parentId]);
        return $stmt->fetchAll();
    }

    public function allProducts(): array
    {
        if (!$this->pdo) return $this->demo['products'];
        return $this->pdo->query($this->productSelect() . " WHERE p.is_active = 1 ORDER BY p.sort_order, p.name")->fetchAll();
    }

    public function featuredProducts(int $limit): array
    {
        if (!$this->pdo) return array_slice(array_values(array_filter($this->demo['products'], fn($p) => $p['is_featured'])), 0, $limit);
        $stmt = $this->pdo->prepare($this->productSelect() . " WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.sort_order, p.id DESC LIMIT :limit");
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT); $stmt->execute();
        return $stmt->fetchAll();
    }

    public function productsByCategory(int $categoryId): array
    {
        if (!$this->pdo) {
            $childIds = array_column($this->subCategories($categoryId), 'id');
            $ids = array_map('intval', array_merge([$categoryId], $childIds));
            return array_values(array_filter($this->demo['products'], fn($p) => in_array((int)$p['category_id'], $ids, true)));
        }
        $stmt = $this->pdo->prepare($this->productSelect() . " WHERE p.is_active = 1 AND (p.category_id = :id OR c.parent_id = :id) ORDER BY p.sort_order, p.name");
        $stmt->execute(['id' => $categoryId]); return $stmt->fetchAll();
    }

    public function productBySlug(string $slug): ?array
    {
        if (!$this->pdo) {
            foreach ($this->demo['products'] as $item) if ($item['slug'] === $slug) return $item;
            return null;
        }
        $stmt = $this->pdo->prepare($this->productSelect() . " WHERE p.slug = :slug AND p.is_active = 1 LIMIT 1");
        $stmt->execute(['slug' => $slug]); return $stmt->fetch() ?: null;
    }

    public function relatedProducts(int $categoryId, int $excludeId, int $limit): array
    {
        return array_slice(array_values(array_filter($this->productsByCategory($categoryId), fn($p) => (int)$p['id'] !== $excludeId)), 0, $limit);
    }

    public function search(string $query): array
    {
        if (!$this->pdo) {
            $lower = static fn(string $value): string => function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value);
            $needle = $lower($query);
            return array_values(array_filter($this->demo['products'], fn($p) => str_contains($lower($p['name'] . ' ' . $p['sku']), $needle)));
        }
        $stmt = $this->pdo->prepare($this->productSelect() . " WHERE p.is_active = 1 AND (p.name LIKE :q OR p.sku LIKE :q OR p.description LIKE :q) ORDER BY p.name LIMIT 50");
        $stmt->execute(['q' => '%' . $query . '%']); return $stmt->fetchAll();
    }

    private function productSelect(): string
    {
        return "SELECT p.*, c.name category_name, c.slug category_slug, b.name brand_name,
            (SELECT ml.url FROM marketplace_links ml WHERE ml.product_id=p.id AND ml.is_active=1 ORDER BY ml.sort_order,ml.id LIMIT 1) marketplace_url,
            COALESCE((SELECT pi.image_path FROM product_images pi WHERE pi.product_id=p.id ORDER BY pi.is_primary DESC, pi.sort_order LIMIT 1), p.image_path) image_path
            FROM products p JOIN categories c ON c.id=p.category_id LEFT JOIN brands b ON b.id=p.brand_id";
    }
}
