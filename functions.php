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
function custom_search_form() {
    ob_start();
    ?>
    <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url(home_url('/')); ?>">
        <div>
            <label class="screen-reader-text" for="s"><?php echo _x('Search for:', 'label'); ?></label>
            <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" />
        </div>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.querySelector('#s');
                const form = document.querySelector('#searchform');

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
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_search', 'custom_search_form');

function get_country_from_ip($ip) {
    $url = 'http://ip-api.com/json/' . $ip;
    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return 'Unable to retrieve data';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if ($data['status'] === 'success') {
        return $data['country'];
    } else {
        return 'Unable to retrieve country';
    }
}

class Custom_Walker_Nav_Menu extends Walker_Nav_Menu {

    // Start Level
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
    }

    // Start Element
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        // Add 'nav-item' class to <li>
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'nav-item';

        // Check if item has children
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'dropdown';
        }

        // Join the classes
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        // Start the <li> element
        $output .= $indent . '<li' . $class_names . '>';

        // Link attributes
        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : '';

        // Add 'nav-link' class to <a>
        $atts['class'] = 'nav-link';
        if (in_array('menu-item-has-children', $classes)) {
            $atts['class'] .= ' dropdown-toggle';
            $atts['data-toggle'] = 'dropdown';
        }

        // Prepare the link attributes
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        // Build the item output
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        // Finalize the output
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

