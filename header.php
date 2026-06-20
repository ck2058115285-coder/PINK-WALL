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
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?><?php $this->options->title(); ?></title>
    
    <!-- 引入核心样式表 -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>">
    
    <!-- 站点自适应与 Meta 申明 -->
    <?php $this->header(); ?>
    
    <!-- 立即设置日夜模式以防止屏幕闪烁 -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>

<header class="site-header">
    <div class="container nav-container">
        <!-- 站点 LOGO 或名称 -->
        <a href="<?php $this->options->siteUrl(); ?>" class="site-brand">
            <?php if ($this->options->logoUrl): ?>
                <img src="<?php $this->options->logoUrl(); ?>" alt="<?php $this->options->title(); ?>" style="height: 40px; display: block;">
            <?php else: ?>
                <?php $this->options->title(); ?><span>.</span>
            <?php endif; ?>
        </a>
        
        <!-- 导航菜单与主题切换按钮 -->
        <div class="site-nav">
            <a href="<?php $this->options->siteUrl(); ?>" class="nav-link <?php if($this->is('index')): ?>active<?php endif; ?>"><?php _e('首页'); ?></a>
            
            <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
            <?php while($pages->next()): ?>
                <a href="<?php $pages->permalink(); ?>" class="nav-link <?php if($this->is('page', $pages->slug)): ?>active<?php endif; ?>" title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a>
            <?php endwhile; ?>
            
            <!-- 日夜模式切换按钮 -->
            <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Dark Mode">
                <!-- 太阳图标 (暗色模式时显示，点击切换到白天) -->
                <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                </svg>
                <!-- 月亮图标 (亮色模式时显示，点击切换到黑夜) -->
                <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                </svg>
            </button>
        </div>
    </div>
</header>
