<?php

$links = get_posts(
    array(
                        'post_type'   => 'wpmcrawler_links',
                        'numberposts' => -1,
                    )
);

echo '<div class="wrap">';
echo '<div class="card">';
echo '<h3> Results </h3>';
foreach ($links as $post_link) {
    echo '<p>' . $post_link->post_content . '</p>';
}

echo '</div></div>';
