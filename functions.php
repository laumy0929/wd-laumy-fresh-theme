<?php
// Theme setup
add_action('after_setup_theme', function () {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', ['search-form', 'caption', 'comment-form', 'comment-list', 'gallery']);
	register_nav_menus([
		'primary' => 'Primary Menu',
	]);
});

// Enqueue assets
add_action('wp_enqueue_scripts', function () {
	$theme_uri = get_template_directory_uri();
	$theme_dir = get_template_directory();
	$style_ver = @filemtime($theme_dir . '/style.css') ?: wp_get_theme()->get('Version');
	$js_ver    = @filemtime($theme_dir . '/assets/js/theme.js') ?: wp_get_theme()->get('Version');
	wp_enqueue_style('laumy-fresh-style', $theme_uri . '/style.css', [], $style_ver);
	wp_enqueue_script('laumy-fresh-theme', $theme_uri . '/assets/js/theme.js', [], $js_ver, true);
	wp_localize_script('laumy-fresh-theme', 'LaumyFresh', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
	]);
	if ( is_singular() && comments_open() && get_option('thread_comments') ) {
		wp_enqueue_script('comment-reply');
	}
});

// Helper: get first image of content or fallback or featured
function laumy_fresh_get_thumbnail_url($post_id = null) {
	$post_id = $post_id ?: get_the_ID();
	if (has_post_thumbnail($post_id)) {
		$img = get_the_post_thumbnail_url($post_id, 'medium');
		if ($img) return $img;
	}
	$content = get_post_field('post_content', $post_id);
	if ($content) {
		if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $m)) {
			return esc_url($m[1]);
		}
	}
	return get_template_directory_uri() . '/assets/images/default-thumbnail.jpg';
}

// Build categories from primary menu order (two-level)
function laumy_fresh_get_menu_categories() {
	$locations = get_nav_menu_locations();
	if (!isset($locations['primary'])) return [];
	$menu = wp_get_nav_menu_object($locations['primary']);
	if (!$menu) return [];
	$items = wp_get_nav_menu_items($menu->term_id);
	if (!$items) return [];

	// First pass: create nodes for category items keyed by menu item ID
	$nodes = [];
	foreach ($items as $item) {
		if ($item->object !== 'category') continue;
		$nodes[$item->ID] = [
			'id' => (int)$item->object_id,
			'title' => $item->title,
			'url' => get_category_link($item->object_id),
			'parent' => (int)$item->menu_item_parent,
			'children' => [],
		];
	}
	// Second pass: attach children to parents and keep top-level order
	$tree = [];
	foreach ($items as $item) {
		if (!isset($nodes[$item->ID])) continue;
		$node = &$nodes[$item->ID];
		if ($node['parent'] === 0) {
			$tree[] = &$node;
		} else if (isset($nodes[$node['parent']])) {
			$nodes[$node['parent']]['children'][] = &$node;
		}
		unset($node);
	}
	return $tree;
}

// Widgets: simple profile fields in Customizer
add_action('customize_register', function($wp_customize){
	$wp_customize->add_section('laumy_profile', [
		'title' => '作者信息',
		'priority' => 30,
	]);
	$fields = [
		['profile_name','昵称','laumy'],
		['profile_job','职业','打工人'],
		['profile_desc','描述','为学日益，为道日损'],
		['profile_image','头像', get_template_directory_uri() . '/assets/images/default-profile.svg'],
	];
	foreach ($fields as [$id,$label,$default]) {
		$wp_customize->add_setting($id, ['default' => $default]);
		$wp_customize->add_control($id, [
			'label' => $label,
			'section' => 'laumy_profile',
			'settings' => $id,
			'type' => $id === 'profile_image' ? 'url' : 'text',
		]);
	}
});

// Multibyte-safe excerpt by characters
function laumy_fresh_excerpt_chars($content, $chars = 120) {
	$text = wp_strip_all_tags( strip_shortcodes( $content ) );
	$text = preg_replace('/\s+/u', ' ', $text);
	$text = trim($text);
	if (function_exists('mb_substr')) {
		$cut = mb_substr($text, 0, $chars, 'UTF-8');
	} else {
		$cut = substr($text, 0, $chars);
	}
	if (function_exists('mb_strlen')) {
		$len = mb_strlen($text, 'UTF-8');
	} else {
		$len = strlen($text);
	}
	return $len > $chars ? ($cut . '...') : $cut;
}

// Unlimited (no truncation) clean excerpt for CSS line-clamp
function laumy_fresh_excerpt_full($content) {
	$text = wp_strip_all_tags( strip_shortcodes( $content ) );
	$text = preg_replace('/\s+/u', ' ', $text);
	return trim($text);
}

// Category post count (optionally include children)
function laumy_fresh_category_count($term_id, $include_children = false) {
	$term = get_term($term_id, 'category');
	if (is_wp_error($term) || !$term) return 0;
	$count = (int) $term->count;
	if ($include_children) {
		$children = get_term_children($term_id, 'category');
		if (!is_wp_error($children)) {
			foreach ($children as $child_id) {
				$child = get_term($child_id, 'category');
				if (!is_wp_error($child) && $child) $count += (int)$child->count;
			}
		}
	}
	return $count;
}

// Site stats: post count and total views (from post meta keys). Cached via transient.
function laumy_fresh_get_site_stats() {
	$posts = (int) wp_count_posts()->publish;
	$views = get_transient('laumy_total_views');
	if ($views === false) {
		$views = 0;
		$q = new WP_Query([
			'post_type' => 'post',
			'post_status' => 'publish',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'no_found_rows' => true,
		]);
		$keys = ['post_views_count','views','view_count','_views'];
		foreach ($q->posts as $pid) {
			foreach ($keys as $k) {
				$v = (int) get_post_meta($pid, $k, true);
				if ($v) { $views += $v; break; }
			}
		}
		wp_reset_postdata();
		set_transient('laumy_total_views', $views, 10 * MINUTE_IN_SECONDS);
	}
	return [
		'posts' => $posts,
		'views' => $views,
		'posts_text' => number_format_i18n($posts),
		'views_text' => number_format_i18n($views),
	];
}

// 获取文章热度（基于浏览量）
function laumy_fresh_get_post_heat($post_id = null) {
	if (!$post_id) {
		$post_id = get_the_ID();
	}
	if (!$post_id) return 0;
	
	// 尝试获取浏览量
	$keys = ['post_views_count', 'views', 'view_count', '_views'];
	$views = 0;
	foreach ($keys as $k) {
		$v = (int) get_post_meta($post_id, $k, true);
		if ($v) {
			$views = $v;
			break;
		}
	}
	
	// 如果没有浏览量，返回一个基础热度
	if ($views == 0) {
		$views = rand(100, 800); // 随机热度，实际使用时可以基于其他指标
	}
	
	return $views;
}

// --- Views increment on single post ---
function laumy_fresh_is_bot_user_agent() {
	$ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
	if ($ua === '') return true;
	$bots = ['bot', 'spider', 'crawl', 'slurp', 'bingpreview', 'yandex', 'duckduckbot', 'baiduspider'];
	foreach ($bots as $b) {
		if (strpos($ua, $b) !== false) return true;
	}
	return false;
}

function laumy_fresh_increment_post_views() {
	if (!is_single() || is_preview()) return;
	if (laumy_fresh_is_bot_user_agent()) return;
	$post_id = get_queried_object_id();
	if (!$post_id) return;
	$cookie = 'lv_' . $post_id;
	if (isset($_COOKIE[$cookie])) return; // prevent rapid repeat counts
	$keys = ['post_views_count','views','view_count','_views'];
	$chosen = 'post_views_count';
	foreach ($keys as $k) { if (metadata_exists('post', $post_id, $k)) { $chosen = $k; break; } }
	$views = (int) get_post_meta($post_id, $chosen, true);
	update_post_meta($post_id, $chosen, $views + 1);
	setcookie($cookie, '1', time() + 6 * HOUR_IN_SECONDS, '/');
	// clear cached total
	delete_transient('laumy_total_views');
}
add_action('wp', 'laumy_fresh_increment_post_views');

?>
