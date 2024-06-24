function create_custom_wp_role() {
    // Add a new role with specific capabilities
    add_role('content_editor', 'Content Editor', array(
        'read' => true, // true allows this capability
        'edit_posts' => true,
        'edit_pages' => true,
        'publish_posts' => true,
        'publish_pages' => true,
        'edit_others_posts' => true,
        'edit_others_pages' => true,
        'delete_posts' => false,
        'delete_pages' => false,
        'delete_others_posts' => false,
        'delete_others_pages' => false,
        'delete_published_posts' => false,
        'delete_published_pages' => false,
        'delete_private_posts' => false,
        'delete_private_pages' => false,
        'edit_private_posts' => false,
        'edit_private_pages' => false,
        'read_private_posts' => false,
        'read_private_pages' => false,
    ));
}

// Hook into the 'after_setup_theme' action to ensure the role is created
add_action('after_setup_theme', 'create_custom_wp_role');
