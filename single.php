<?php get_header(); ?>
<main class="main-container">
	<!-- Left: TOC -->
	<aside class="left-sidebar">
		<div class="toc-card">
			<div class="toc-header">
				<span class="text">文章目录</span>
				<span class="toc-toggle" style="font-size: 18px; color: #666;">☰</span>
			</div>
			<div class="toc-body">
				<?php
				$content = apply_filters('the_content', get_post_field('post_content', get_the_ID()));
				preg_match_all('/<h([2-5])[^>]*>(.*?)<\/h[2-5]>/i', $content, $m);
				$tree = [];
				$current = null;
				$current_h3 = null;
				$current_h4 = null;
				
				foreach ($m[0] as $i => $full) {
					$level = intval($m[1][$i]);
					$title = trim(strip_tags($m[2][$i]));
					$anchor = 'sec-' . $i;
					
					if ($level === 2) {
						// H2作为主要章节
						$tree[] = ['title'=>$title,'anchor'=>$anchor,'children'=>[]];
						$current = count($tree)-1;
						$current_h3 = null;
						$current_h4 = null;
					} elseif ($level === 3 && $current !== null) {
						// H3作为H2的子章节
						$tree[$current]['children'][] = ['title'=>$title,'anchor'=>$anchor,'children'=>[]];
						$current_h3 = count($tree[$current]['children'])-1;
						$current_h4 = null;
					} elseif ($level === 4 && $current !== null && $current_h3 !== null) {
						// H4作为H3的子章节
						$tree[$current]['children'][$current_h3]['children'][] = ['title'=>$title,'anchor'=>$anchor,'children'=>[]];
						$current_h4 = count($tree[$current]['children'][$current_h3]['children'])-1;
					} elseif ($level === 5 && $current !== null && $current_h3 !== null && $current_h4 !== null) {
						// H5作为H4的子章节
						$tree[$current]['children'][$current_h3]['children'][$current_h4]['children'][] = ['title'=>$title,'anchor'=>$anchor];
					}
				}
				if ($tree) {
					if (!function_exists('laumy_render_toc_nodes')) {
						function laumy_render_toc_nodes($nodes, $prefix = '', $isRoot = true) {
							echo $isRoot ? '<ul class="toc-list">' : '<ul class="toc-children">';
							$idx = 1;
							foreach ($nodes as $n) {
								$number = $prefix === '' ? (string)$idx : $prefix . '.' . $idx;
								echo '<li class="toc-item">';
								echo '<a class="toc-link" href="#'.$n['anchor'].'"><span class="toc-num">'.$number.'.</span>'.esc_html($n['title']).'</a>';
								if (!empty($n['children'])) {
									laumy_render_toc_nodes($n['children'], $number, false);
								}
								echo '</li>';
								$idx++;
							}
							echo '</ul>';
						}
					}
					laumy_render_toc_nodes($tree);
				} else {
					echo '<div class="card-body">暂无目录</div>';
				}
				?>
			</div>
		</div>
	</aside>

	<!-- Content -->
	<section class="content-area">
		<article class="single-article">
			<h1 class="single-title"><?php the_title(); ?></h1>
			<div class="single-meta">
				<span class="meta-item">
					<span class="meta-icon">🕒</span>
					<?php echo esc_html(get_the_date('Y-m-d')); ?>
				</span>
				<span class="meta-item">
					<span class="meta-icon">📁</span>
					<?php the_category(', '); ?>
				</span>
				<span class="meta-item">
					<span class="meta-icon">👤</span>
					<?php echo esc_html(get_the_author()); ?>
				</span>
			</div>
			<div class="single-body">
				<?php
				// inject anchors to headings
				$raw = get_the_content();
				$idx = -1;
				$raw = preg_replace_callback('/<h([1-6])([^>]*)>(.*?)<\/h[1-6]>/i', function($mm) use (&$idx){
					$idx++;
					return '<h'.$mm[1].' id="sec-'.$idx.'"'.$mm[2].'>'.$mm[3].'</h'.$mm[1].'>';
				}, $raw);
				echo apply_filters('the_content', $raw);
				?>
			</div>
		</article>
	</section>

	<!-- Right: switch to 最新文章 on single -->
	<aside class="right-sidebar">
		<div class="card profile-card">
			<div class="card-header">个人资料</div>
			<div class="card-body">
				<?php $stats = laumy_fresh_get_site_stats(); ?>
				<img class="profile-avatar" src="<?php echo esc_url(get_theme_mod('profile_image', get_template_directory_uri() . '/assets/images/default-profile.svg')); ?>" alt="avatar" />
				<div class="profile-name-wrap">
					<span class="profile-name"><?php echo esc_html(get_theme_mod('profile_name', 'laumy')); ?></span>
					<span class="profile-job-badge"><?php echo esc_html(get_theme_mod('profile_job', '打工人')); ?></span>
				</div>
				<p class="profile-desc"><?php echo esc_html(get_theme_mod('profile_desc', '为学日益，为道日损')); ?></p>
				<div class="profile-stats">
					<div class="stat-item"><span class="stat-label">文章数</span><span class="stat-number"><?php echo $stats['posts_text']; ?></span></div>
					<div class="stat-item"><span class="stat-label">阅读数</span><span class="stat-number"><?php echo $stats['views_text']; ?></span></div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">最新文章</div>
			<div class="card-body">
				<ul class="recent-list">
					<?php $recent = new WP_Query(['posts_per_page'=>10, 'orderby'=>'modified', 'order'=>'DESC', 'ignore_sticky_posts'=>true, 'no_found_rows'=>true]);
					if ($recent->have_posts()): while ($recent->have_posts()): $recent->the_post();
					$thumb = laumy_fresh_get_thumbnail_url(); ?>
					<li class="recent-item">
						<a href="<?php the_permalink(); ?>"><img class="recent-thumb" src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" /></a>
						<div>
							<p class="recent-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
							<div class="recent-date"><?php echo esc_html(get_the_modified_date('Y-m-d')); ?></div>
						</div>
					</li>
					<?php endwhile; endif; wp_reset_postdata(); ?>
				</ul>
			</div>
		</div>
	</aside>
</main>
<?php get_footer(); ?>
