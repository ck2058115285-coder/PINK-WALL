<?php
/**
 * PINK WALL Theme File
 *
 * @package PINK WALL
 * @author RK <ck2058115285@gmail.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2026 RK. All rights reserved.
 */
 if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<main class="container">
    <article class="post-detail">
        <!-- 页头信息 -->
        <header class="post-header">
            <h1 class="post-title"><?php $this->title(); ?></h1>
        </header>

        <!-- 文章主体内容 -->
        <div class="post-body">
            <?php $this->content(); ?>
        </div>
    </article>
</main>

<?php $this->need('footer.php'); ?>
