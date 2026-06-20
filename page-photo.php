<?php
/**
 * 相册瀑布流 (Photo Gallery)
 *
 * @package custom
 * @author RK <ck2058115285@gmail.com>
 * @version 1.0.0
 * @copyright Copyright (c) 2026 RK. All rights reserved.
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit; 
$this->need('header.php');

// 获取此页面下的所有附件图片
$db = Typecho_Db::get();
$attachments = $db->fetchAll($db->select()
    ->from('table.contents')
    ->where('table.contents.parent = ?', $this->cid)
    ->where('table.contents.type = ?', 'attachment')
    ->order('table.contents.order', Typecho_Db::SORT_ASC)
    ->order('table.contents.cid', Typecho_Db::SORT_ASC));

// 解析附件数据
function get_photo_attachment_data($attachment) {
    $text_raw = $attachment['text'];
    $data = null;
    if (strpos($text_raw, 'a:') === 0 || strpos($text_raw, 'O:') === 0) {
        $data = @unserialize($text_raw);
    } else {
        $data = json_decode($text_raw, true);
    }
    
    $url = '';
    $desc = '';
    $name = '';
    if (is_array($data)) {
        $url = isset($data['path']) ? $data['path'] : '';
        $desc = isset($data['description']) ? $data['description'] : '';
        $name = isset($data['name']) ? $data['name'] : '';
    }
    
    return [
        'url' => Helper::options()->siteUrl . ltrim($url, '/'),
        'desc' => $desc ? $desc : $attachment['title'],
        'name' => $name ? $name : $attachment['title']
    ];
}
?>

<main class="container">
    <!-- 页面标题与引言 -->
    <div class="site-intro" style="margin-bottom: 40px;">
        <h1 class="site-title"><?php $this->title(); ?></h1>
        <?php if ($this->content): ?>
            <div class="post-body" style="font-size: 1rem; color: var(--text-muted); margin-top: 16px;">
                <?php $this->content(); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- 相册瀑布流照片网格 (整个网格作为一个 Lightbox 图集) -->
    <div class="waterfall-grid gallery-card-wrapper">
        <?php if (!empty($attachments)): ?>
            <?php foreach ($attachments as $attachment): ?>
                <?php 
                    $img = get_photo_attachment_data($attachment);
                    // 仅显示图片类型的附件
                    if ($attachment['type'] == 'attachment' && preg_match('/\.(jpg|jpeg|png|gif|webp|svg)/i', $img['url'])):
                ?>
                    <article class="grid-item">
                        <a href="<?php echo htmlspecialchars($img['url'], ENT_QUOTES, 'UTF-8'); ?>" 
                           class="card-link" 
                           data-title="<?php echo htmlspecialchars($img['name'], ENT_QUOTES, 'UTF-8'); ?>" 
                           data-desc="<?php echo htmlspecialchars($img['desc'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="card-img-wrapper">
                                <img class="lazy card-img" 
                                     src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" 
                                     data-src="<?php echo htmlspecialchars(get_image_thumb($img['url'], 600), ENT_QUOTES, 'UTF-8'); ?>" 
                                     alt="<?php echo htmlspecialchars($img['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </a>
                        <?php if ($img['desc'] && $img['desc'] !== $img['name']): ?>
                            <div class="card-content" style="padding: 12px 16px;">
                                <p style="margin: 0; font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; text-align: center;">
                                    <?php echo htmlspecialchars($img['desc'], ENT_QUOTES, 'UTF-8');
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 80px 0; color: var(--text-muted); width: 100%;">
                <p>该页面暂未上传任何照片附件。您可以在后台编辑此页面并上传图片，它们将自动作为相册在此展示。</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php $this->need('footer.php'); ?>
