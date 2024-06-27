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

function custom_search_script() {
    if (is_search() || is_front_page() || is_home()) { // Adjust conditions as needed
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.querySelector('input[name="s"]');
                const form = searchInput.closest('form');

                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }

                function handleInput() {
                    const keywords = searchInput.value.trim().split(/\s+/);
                    if (keywords.length >= 3) {
                        form.submit();
                    }
                }

                const debouncedHandleInput = debounce(handleInput, 300); // 300ms debounce time

                if (searchInput && form) {
                    searchInput.addEventListener('input', debouncedHandleInput);
                }
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'custom_search_script');
<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url(home_url('/')); ?>">
    <div>
        <label class="screen-reader-text" for="s"><?php echo _x('Search for:', 'label'); ?></label>
        <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" />
    </div>
</form>
