<?php
if ( post_password_required() ) { return; }
?>
<div id="comments" class="comments-wrap">
	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title"><?php printf('评论（%1$s）', number_format_i18n(get_comments_number())); ?></h3>
		<ol class="comment-list">
			<?php
			wp_list_comments([
				'style' => 'ol',
				'short_ping' => true,
				'avatar_size' => 40,
				'format' => 'html5',
			]);
			?>
		</ol>
		<?php if ( get_comment_pages_count() > 1 && get_option('page_comments') ) : ?>
			<nav class="comment-nav" role="navigation">
				<div class="nav-previous"><?php previous_comments_link('← 更早的评论'); ?></div>
				<div class="nav-next"><?php next_comments_link('更新的评论 →'); ?></div>
			</nav>
		<?php endif; ?>
	<?php endif; ?>

	<?php 
	$commenter = wp_get_current_commenter();
	$require_name_email = (bool) get_option('require_name_email');
	$placeholder_suffix = $require_name_email ? '（必填）' : '（选填）';
	$req_attr = $require_name_email ? ' required aria-required="true"' : '';
	$author_val = esc_attr( $commenter['comment_author'] );
	$email_val = esc_attr( $commenter['comment_author_email'] );
	$cookies_field = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" /> <label for="wp-comment-cookies-consent">在此浏览器中保存我的显示名称和邮箱地址，以便下次评论时使用。</label></p>';

	comment_form([
		'title_reply' => '发表你的看法',
		'label_submit' => '提交评论',
		'comment_notes_before' => '',
		'class_submit' => 'button',
		'format' => 'html5',
		// place author/email on one row and remove website field
		'fields' => [
			'author' => '<div class="comment-fields-row"><p class="comment-form-author"><input id="author" name="author" type="text" value="'.$author_val.'" placeholder="显示名称'.$placeholder_suffix.'"'.$req_attr.' /></p>',
			'email'  => '<p class="comment-form-email"><input id="email" name="email" type="email" value="'.$email_val.'" placeholder="邮箱'.$placeholder_suffix.'"'.$req_attr.' /></p></div>',
			'cookies'=> $cookies_field,
		],
	]); ?>
</div>
