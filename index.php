<?php
/**
 * PINK WALL Theme File
 *
 * @package PINK WALL
 * @author RK <ck2058115285@gmail.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2026 RK. All rights reserved.
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

    <?php if ($this->is('index') && $this->_currentPage == 1 && (!isset($this->options->showHeroSlider) || $this->options->showHeroSlider != '0')): ?>
        <!-- 首页顶部全景幻灯片巨幕 (由 JavaScript 异步加载并渲染) -->
        <div class="hero-slider-container">
            <div class="hero-slider" id="hero-slider">
                <div class="slide-item active slide-loading" style="background: #111; display: flex; align-items: center; justify-content: center; height: 100%;">
                    <div style="color: rgba(255,255,255,0.6); font-size: 1rem; letter-spacing: 2px;">正在加载摄影作品...</div>
                </div>
            </div>
            <!-- 翻页控制按钮 (加载完成前隐藏) -->
            <button class="slider-arrow prev-arrow" id="prev-arrow" aria-label="Previous Slide" style="display: none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button class="slider-arrow next-arrow" id="next-arrow" aria-label="Next Slide" style="display: none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
            <!-- 轮播控制点 -->
            <div class="slider-dots" id="slider-dots"></div>
            <!-- 向下滚动指示器 -->
            <div class="scroll-down-indicator" id="scroll-down">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="7 13 12 18 17 13"></polyline>
                    <polyline points="7 6 12 11 17 6"></polyline>
                </svg>
            </div>
        </div>
    <?php endif; ?>

<main class="container">

    <!-- 主页顶部横幅/引言 -->
    <div class="site-intro">
        <h1 class="site-title">
            <?php echo $this->options->introTitle ? $this->options->introTitle : $this->options->title; ?>
        </h1>
        <p class="site-desc">
            <?php echo $this->options->introDesc ? $this->options->introDesc : $this->options->description; ?>
        </p>
    </div>

    <!-- 瀑布流网格 -->
    <div class="waterfall-grid">
        <?php if ($this->have()): ?>
            <?php while($this->next()): ?>
                <?php 
                    $images = get_post_images($this, 4);
                    $images_count = count($images);
                    $cover_img = $images_count > 0 ? $images[0] : null;
                    $post_desc = get_post_desc($this);
                ?>
                <article class="grid-item" data-cid="<?php echo $this->cid; ?>">

                    <?php if ($images_count > 1): ?>
                        <!-- 多图画廊集卡片 - 图集封面只展示一张照片，其余隐藏以便灯箱翻页 -->
                        <div class="gallery-card-wrapper">
                            <a href="<?php echo $cover_img; ?>" 
                               class="card-link" 
                               data-title="<?php $this->title(); ?> (1/<?php echo $images_count; ?>)" 
                               data-desc="<?php echo htmlspecialchars($post_desc, ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="card-img-wrapper">
                                    <span class="gallery-badge">
                                        <svg style="width:12px; height:12px; margin-right:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        图集 <?php echo $images_count; ?>张
                                    </span>
                                    <img class="lazy card-img" 
                                         src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" 
                                         data-src="<?php echo get_image_thumb($cover_img, 600); ?>" 
                                         alt="<?php $this->title(); ?>">
                                </div>
                            </a>
                            <!-- 隐藏的其余图集照片链接，用于被 Lightbox 扫描并在灯箱中连续查看 -->
                            <div class="hidden-gallery-links" style="display: none;">
                                <?php for ($img_idx = 1; $img_idx < $images_count; $img_idx++): ?>
                                    <a href="<?php echo $images[$img_idx]; ?>" 
                                       class="card-link" 
                                       data-title="<?php $this->title(); ?> (<?php echo ($img_idx+1); ?>/<?php echo $images_count; ?>)" 
                                       data-desc="<?php echo htmlspecialchars($post_desc, ENT_QUOTES, 'UTF-8'); ?>">
                                    </a>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php elseif ($images_count == 1): ?>
                        <!-- 单图文章卡片 -->
                        <a href="<?php echo $cover_img; ?>" 
                           class="card-link" 
                           data-title="<?php $this->title(); ?>" 
                           data-desc="<?php echo htmlspecialchars($post_desc, ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="card-img-wrapper">
                                <img class="lazy card-img" 
                                     src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" 
                                     data-src="<?php echo get_image_thumb($cover_img, 600); ?>" 
                                     alt="<?php $this->title(); ?>">
                            </div>
                        </a>
                    <?php else: ?>
                        <!-- 纯文本卡片 (点击直接跳转文章) -->
                        <a href="<?php $this->permalink(); ?>" class="card-link-text">
                            <div class="card-text-cover cover-bg-<?php echo (($this->cid % 5) + 1); ?>">
                                <div class="text-cover-inner">
                                    <?php 
                                        $keyword = get_post_keyword($this); 
                                        $is_cn = is_chinese($keyword);
                                    ?>
                                    <span class="text-cover-word <?php echo $is_cn ? 'vertical-text' : ''; ?>">
                                        <?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                    
                    <!-- 卡片下方文字信息与作者 -->
                    <div class="card-content">
                        <h2 class="card-title">
                            <a href="<?php $this->permalink(); ?>"><?php $this->title(); ?></a>
                        </h2>
                        <div class="card-meta-new">
                            <div class="card-author-info">
                                <img class="card-avatar" src="<?php echo Typecho_Common::gravatarUrl($this->author->mail, 32, 'X', 'X', $this->request->isSecure()); ?>" alt="<?php $this->author(); ?>">
                                <span class="card-author-name"><?php $this->author(); ?></span>
                            </div>
                            <span class="card-date-new"><?php $this->date('Y-m-d'); ?></span>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 80px 0; color: var(--text-muted);">
                <p><?php _e('没有找到任何照片或文章。'); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$this->is('index')): ?>
        <!-- 分页导航 -->
        <div class="pagination">
            <?php $this->pageNav(
                _t('&laquo; 上一页'), 
                _t('下一页 &raquo;'), 
                3, 
                '...', 
                array(
                    'wrapTag' => 'div',
                    'wrapClass' => 'pagination',
                    'itemTag' => 'span',
                    'textTag' => 'span',
                    'currentClass' => 'page-current',
                    'prevClass' => 'page-btn prev',
                    'nextClass' => 'page-btn next'
                )
            ); ?>
        </div>
    <?php else: ?>
        <!-- 首页到底提示 -->
        <div class="page-end-tip" style="text-align: center; padding: 40px 0 60px 0; color: var(--text-muted); font-size: 0.95rem; letter-spacing: 2px;">
            <?php _e('别滑了，没有了'); ?>
        </div>
    <?php endif; ?>
</main>

<?php $this->need('footer.php'); ?>
