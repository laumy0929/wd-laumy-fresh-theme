<?php get_header(); ?>
<main class="main-container">
	<!-- Left Sidebar: Categories from menu order -->
	<aside class="left-sidebar">
		<div class="categories-card">
			<div class="categories-title">
				<span class="text">分类专栏</span>
				<span class="category-toggle" style="font-size: 18px; color: #666;">☰</span>
			</div>
			<ul class="categories-list">
				<?php $cats = laumy_fresh_get_menu_categories(); ?>
				<?php foreach ($cats as $cat): ?>
					<li class="category-item">
						<div class="category-header">
							<?php if (!empty($cat['children'])): ?>
							<span class="category-arrow">▶</span>
							<?php else: ?>
							<span class="category-arrow" style="visibility: hidden;">▶</span>
							<?php endif; ?>
							<a class="category-link" href="<?php echo esc_url($cat['url']); ?>"><?php echo esc_html($cat['title']); ?></a>
							<span class="category-count"><?php echo laumy_fresh_category_count($cat['id'], true); ?>篇</span>
						</div>
						<?php if (!empty($cat['children'])): ?>
						<ul class="subcategory-list">
							<?php foreach ($cat['children'] as $sub): ?>
								<li><a href="<?php echo esc_url($sub['url']); ?>"><?php echo esc_html($sub['title']); ?><span class="subcategory-count"><?php echo laumy_fresh_category_count($sub['id'], false); ?>篇</span></a></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</aside>

	<!-- Content Area: Latest posts with pagination -->
	<section class="content-area">
		<div class="posts-card">
			<div class="posts-header">最新文章</div>
			<ul class="posts-list">
				<?php
				$paged = get_query_var('paged') ? get_query_var('paged') : 1;
				$query = new WP_Query([
					'posts_per_page' => 10,
					'paged' => $paged,
				]);
				if ($query->have_posts()): while ($query->have_posts()): $query->the_post();
					$thumb = laumy_fresh_get_thumbnail_url();
				?>
				<li class="post-item">
					<a href="<?php the_permalink(); ?>">
						<img class="post-thumb" src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
					</a>
					<div>
						<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<div class="post-excerpt"><?php echo esc_html( laumy_fresh_excerpt_full(get_the_content()) ); ?></div>
						<div class="post-meta">
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
					</div>
				</li>
				<?php endwhile; endif; wp_reset_postdata(); ?>
			</ul>
			<div class="pagination"><?php echo paginate_links(['total' => $query->max_num_pages, 'current' => $paged]); ?></div>
		</div>
	</section>

	<!-- Right Sidebar: Profile + Recent updates -->
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
			<div class="card-header">最近更新</div>
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
