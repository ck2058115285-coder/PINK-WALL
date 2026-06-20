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
            <div class="post-meta">
                <span>发布于 <?php $this->date('Y-m-d'); ?></span>
                <span>/</span>
                <span>作者 <?php $this->author(); ?></span>
                <?php if ($this->category): ?>
                    <span>/</span>
                    <span>分类 <?php $this->category(',', true); ?></span>
                <?php endif; ?>
            </div>
        </header>

        <!-- 文章主体内容 -->
        <div class="post-body">
            <?php $this->content(); ?>
        </div>
    </article>
</main>

<?php $this->need('footer.php'); ?>
