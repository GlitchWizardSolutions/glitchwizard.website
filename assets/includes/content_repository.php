<?php
// content_repository.php - centralized DB access for normalized content_items/pages_metadata
// Created: 2025-08-20

if(!isset($pdo)) {
    // Expect caller to have included a bootstrap that defines $pdo
}

function ci_get_items(PDO $pdo, string $area): array {
    $stmt = $pdo->prepare("SELECT id, area, slug, title, body, icon, position, active FROM content_items WHERE area = :area ORDER BY position, id");
    $stmt->execute([':area'=>$area]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function ci_get_active_items(PDO $pdo, string $area): array {
    $stmt = $pdo->prepare("SELECT id, area, slug, title, body, icon, position FROM content_items WHERE area = :area AND active=1 ORDER BY position, id");
    $stmt->execute([':area'=>$area]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function ci_get_section_meta(PDO $pdo, string $slug): ?array {
    $stmt = $pdo->prepare("SELECT id, slug, title, body FROM content_items WHERE area='section' AND slug = :slug LIMIT 1");
    $stmt->execute([':slug'=>$slug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function ci_upsert_section_meta(PDO $pdo, string $slug, ?string $title, ?string $body): void {
    $stmt = $pdo->prepare("INSERT INTO content_items (area, slug, title, body, position, active, created_at, updated_at)
        VALUES ('section', :slug, :title, :body, 0, 1, NOW(), NOW())
        ON DUPLICATE KEY UPDATE title = VALUES(title), body = VALUES(body), updated_at = NOW()");
    $stmt->execute([':slug'=>$slug, ':title'=>$title, ':body'=>$body]);
}

function ci_update_item(PDO $pdo, int $id, array $fields): bool {
    $allowed = ['title','body','icon','position','active'];
    $sets=[];$params=[':id'=>$id];
    foreach($fields as $k=>$v){
        if(in_array($k,$allowed,true)) { $sets[]="$k=:$k"; $params[":$k"]=$v; }
    }
    if(!$sets) return false;
    $sql = "UPDATE content_items SET ".implode(',', $sets).", updated_at=NOW() WHERE id=:id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

// ---- Pages Metadata & Payload Helpers ----
function pages_get_metadata(PDO $pdo, string $slug): ?array {
    $stmt = $pdo->prepare("SELECT slug, meta_title, meta_description FROM pages_metadata WHERE slug = :slug LIMIT 1");
    $stmt->execute([':slug'=>$slug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function pages_upsert_metadata(PDO $pdo, string $slug, ?string $meta_title, ?string $meta_description): void {
    // pages_metadata has: id, slug, meta_title, meta_description, updated_at
    $stmt = $pdo->prepare("INSERT INTO pages_metadata (slug, meta_title, meta_description)
        VALUES (:slug, :mt, :md)
        ON DUPLICATE KEY UPDATE meta_title = VALUES(meta_title), meta_description = VALUES(meta_description), updated_at = NOW()");
    $stmt->execute([':slug'=>$slug, ':mt'=>$meta_title, ':md'=>$meta_description]);
}

// Store multi-part page content as JSON in content_items with area='page' and slug = page slug
function pages_get_payload(PDO $pdo, string $slug): array {
    $stmt = $pdo->prepare("SELECT body FROM content_items WHERE area='page' AND slug = :slug LIMIT 1");
    $stmt->execute([':slug'=>$slug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$row) return [];
    $json = json_decode($row['body'] ?? '[]', true);
    return is_array($json)? $json : [];
}

function pages_upsert_payload(PDO $pdo, string $slug, array $payload): void {
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $stmt = $pdo->prepare("INSERT INTO content_items (area, slug, title, body, icon, position, active, created_at, updated_at)
        VALUES ('page', :slug, '', :body, '', 0, 1, NOW(), NOW())
        ON DUPLICATE KEY UPDATE body = VALUES(body), updated_at = NOW()");
    $stmt->execute([':slug'=>$slug, ':body'=>$json]);
}

?>
