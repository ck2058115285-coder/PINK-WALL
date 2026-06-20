<?php
/**
 * 友情链接 (Friend Links)
 *
 * @package custom
 * @author RK <ck2058115285@gmail.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2026 RK. All rights reserved.
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit; 
$this->need('header.php');

// 将内容按 <hr> 或 --- 分割线拆分为“介绍”与“友链配置”
$content = $this->content;
$parts = preg_split('/(?:<hr\s*\/?>|(?:\r?\n|<br\s*\/?>|^)\s*-{3,}\s*(?:\r?\n|<br\s*\/?>|$))/i', $content, 2);
$intro = $parts[0];
$links_raw = isset($parts[1]) ? $parts[1] : '';

// 解析友情链接数据
$links = array();
if (!empty($links_raw)) {
    // 替换 </p>、<br>、</li> 标签为换行符以保留物理行，然后剥离 HTML 标签
    $clean_html = preg_replace('/<\/p>|<br\s*\/?>|<\/li>/i', "\n", $links_raw);
    $clean_text = strip_tags($clean_html);
    $lines = explode("\n", $clean_text);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        // 剥离 Markdown 列表的前缀 - 或 *
        $line = preg_replace('/^[-\*\+]\s+/', '', $line);
        
        $parts = explode('|', $line);
        if (count($parts) >= 2) {
            $name = trim($parts[0]);
            $url = trim($parts[1]);
            $avatar = isset($parts[2]) ? trim($parts[2]) : '';
            $desc = isset($parts[3]) ? trim($parts[3]) : '';
            
            // 简单清洗一下头像和链接
            $url = htmlspecialchars_decode($url);
            if (!empty($avatar)) {
                $avatar = htmlspecialchars_decode($avatar);
            }
            
            $links[] = array(
                'name' => $name,
                'url' => $url,
                'avatar' => $avatar,
                'desc' => $desc
            );
        }
    }
}
?>

<main class="container">
    <div class="links-container">
        <!-- 页面标题与介绍 -->
        <div class="site-intro" style="margin-bottom: 40px;">
            <h1 class="site-title"><?php $this->title(); ?></h1>
            <?php if (!empty($intro)): ?>
                <div class="post-body" style="font-size: 1.05rem; color: var(--text-muted); margin-top: 24px; text-align: left; max-width: 800px; margin-left: auto; margin-right: auto; line-height: 1.8;">
                    <?php echo $intro; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 友链卡片网格 -->
        <?php if (!empty($links)): ?>
            <div class="links-grid">
                <?php foreach ($links as $link): ?>
                    <div class="link-card" onclick="window.open('<?php echo htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>', '_blank')">
                        <div class="link-card-avatar-wrapper">
                            <?php if (!empty($link['avatar'])): ?>
                                <img class="link-card-avatar" src="<?php echo htmlspecialchars($link['avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($link['name'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <span class="link-card-letter-avatar" style="display: none;"><?php echo mb_substr($link['name'], 0, 1, 'utf-8'); ?></span>
                            <?php else: ?>
                                <span class="link-card-letter-avatar"><?php echo mb_substr($link['name'], 0, 1, 'utf-8'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="link-card-info">
                            <h2 class="link-card-name"><?php echo htmlspecialchars($link['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                            <p class="link-card-desc" title="<?php echo htmlspecialchars($link['desc'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo !empty($link['desc']) ? htmlspecialchars($link['desc'], ENT_QUOTES, 'UTF-8') : '这家伙很懒，什么都没写...'; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 24px; border: 1px dashed var(--border-color); border-radius: 16px; color: var(--text-muted); max-width: 600px; margin: 40px auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary-color); margin-bottom: 16px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                <h3 style="margin-bottom: 12px; color: var(--text-color);">还没有配置任何友情链接</h3>
                <p style="font-size: 0.9rem; line-height: 1.6; text-align: left; padding: 0 16px;">
                    <strong>如何配置？</strong><br>
                    编辑此独立页面，在正文中添加分割线 <code>---</code>，在分割线下方按以下格式按行填入您的友链信息（每行一条）：<br>
                    <code>名称|链接|头像|描述</code><br><br>
                    <strong>示例：</strong><br>
                    <code>Lopwon|https://www.lopwon.com|https://www.lopwon.com/avatar.jpg|主题作者的博客</code>
                </p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php $this->need('footer.php'); ?>
