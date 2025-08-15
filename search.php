<?php get_header(); ?>
<main class="main-container">
	<aside class="left-sidebar">
		<div class="categories-card">
			<div class="categories-title"><span class="text">分类专栏</span><span class="category-toggle">≡</span></div>
			<ul class="categories-list">
				<?php $cats = laumy_fresh_get_menu_categories(); ?>
				<?php foreach ($cats as $cat): ?>
					<li class="category-item">
						<div class="category-header">
							<a class="category-link" href="<?php echo esc_url($cat['url']); ?>"><?php echo esc_html($cat['title']); ?></a>
							<span>▼</span>
						</div>
						<?php if (!empty($cat['children'])): ?>
						<ul class="subcategory-list">
							<?php foreach ($cat['children'] as $sub): ?>
								<li><a href="<?php echo esc_url($sub['url']); ?>"><?php echo esc_html($sub['title']); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</aside>

	<section class="content-area">
		<div class="posts-card">
			<div class="posts-header">搜索：<?php echo esc_html(get_search_query()); ?></div>
			<ul class="posts-list">
				<?php if (have_posts()): while (have_posts()): the_post(); $thumb = laumy_fresh_get_thumbnail_url(); ?>
				<li class="post-item">
					<a href="<?php the_permalink(); ?>">
						<img class="post-thumb" src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
					</a>
					<div>
						<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<div class="post-excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_content()), 50)); ?></div>
						<div class="post-meta">
							<?php echo esc_html(get_the_author()); ?> | <?php echo esc_html(get_the_date('Y-m-d')); ?>
						</div>
					</div>
				</li>
				<?php endwhile; else: ?>
				<li>未找到结果</li>
				<?php endif; ?>
			</ul>
			<div class="pagination"><?php echo paginate_links(); ?></div>
		</div>
	</section>

	<aside class="right-sidebar">
		<div class="card">
			<div class="card-header">个人资料</div>
			<div class="card-body">
				<img class="profile-avatar" src="<?php echo esc_url(get_theme_mod('profile_image', get_template_directory_uri() . '/assets/images/default-profile.jpg')); ?>" alt="avatar" />
				<div class="profile-name"><?php echo esc_html(get_theme_mod('profile_name', 'laumy')); ?></div>
				<div class="profile-occupation"><?php echo esc_html(get_theme_mod('profile_job', '打工人')); ?></div>
				<p class="profile-desc"><?php echo esc_html(get_theme_mod('profile_desc', '为学日益，为道日损')); ?></p>
			</div>
		</div>
	</aside>
</main>
<?php get_footer(); ?>
