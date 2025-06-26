<?php
declare(strict_types=1);

namespace App\Model;

use App\Database\Connection;
use PDO;

final class SampleArticleModel
{
    /**
     * Récupère tous les articles
     *
     * @return array
     */
    public static function getAll(): array
    {
        $sql = "SELECT id, title, content, created_at FROM articles ORDER BY created_at DESC";
        $stmt = Connection::get()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un article par son ID
     *
     * @param int $id
     * @return array|null
     */
    public static function find(int $id): ?array
    {
        $sql = "SELECT * FROM articles WHERE id = :id";
        $stmt = Connection::get()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Insère un nouvel article
     *
     * @param string $title
     * @param string $content
     * @return bool
     */
    public static function insert(string $title, string $content): bool
    {
        $sql = "INSERT INTO articles (title, content, created_at) VALUES (:title, :content, NOW())";
        $stmt = Connection::get()->prepare($sql);
        return $stmt->execute([
            'title'   => $title,
            'content' => $content,
        ]);
    }

    /**
     * Met à jour un article
     *
     * @param int $id
     * @param string $title
     * @param string $content
     * @return bool
     */
    public static function update(int $id, string $title, string $content): bool
    {
        $sql = "UPDATE articles SET title = :title, content = :content WHERE id = :id";
        $stmt = Connection::get()->prepare($sql);
        return $stmt->execute([
            'id'      => $id,
            'title'   => $title,
            'content' => $content,
        ]);
    }

    /**
     * Supprime un article
     *
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $sql = "DELETE FROM articles WHERE id = :id";
        $stmt = Connection::get()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
