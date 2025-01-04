<?php
class Tag {
    private $db;
    public $id;
    public $name;

    // Constructor to initialize the database connection and optionally load a tag by id
    public function __construct($db, $id = null) {
        $this->db = $db;

        if ($id) {
            $this->loadTagById($id);
        }
    }

    // Load a tag by its id
    public function loadTagById($id) {
        $sql = "SELECT * FROM tags WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $result['id'];
        $this->name = $result['name'];
    }

    // Create a new tag
    public function createTag($name) {
        $sql = "INSERT INTO tags (name) VALUES (:name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    // Update an existing tag
    public function updateTag($name) {
        $sql = "UPDATE tags SET name = :name WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Delete a tag
    public function deleteTag() {
        $sql = "DELETE FROM tags WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Get all tags
    public static function getAllTags($db) {
        $sql = "SELECT * FROM tags";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
