<?php get_header(); ?>
<main class="main-container">
	<section class="content-area" style="grid-column: 1 / -1;">
		<article class="single-article">
			<h1 class="single-title"><?php the_title(); ?></h1>
			<div class="single-body"><?php while(have_posts()): the_post(); the_content(); endwhile; ?></div>
		</article>
	</section>
</main>
<?php get_footer(); ?>
