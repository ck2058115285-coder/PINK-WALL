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

/**
 * 主题配置选项
 */
function themeConfig($form) {
    // 网站 Logo
    $logoUrl = new Typecho_Widget_Helper_Form_Element_Text(
        'logoUrl', 
        NULL, 
        NULL, 
        _t('站点 LOGO 地址'), 
        _t('在这里填入一个图片 URL 地址，留空则默认显示站点名称文本。')
    );
    $form->addInput($logoUrl);
    
    // 主页大标题
    $introTitle = new Typecho_Widget_Helper_Form_Element_Text(
        'introTitle', 
        NULL, 
        _t('探索视觉的美好'), 
        _t('主页大标题'), 
        _t('主页顶部展示的横幅大标题。')
    );
    $form->addInput($introTitle);

    // 主页小标题/描述
    $introDesc = new Typecho_Widget_Helper_Form_Element_Textarea(
        'introDesc', 
        NULL, 
        _t('记录生活，留住瞬间。这里是一个优雅的照片瀑布流展示空间。'), 
        _t('主页描述文字'), 
        _t('主页大标题下方展示的副标题描述。')
    );
    $form->addInput($introDesc);

    // 网站建站时间
    $starttime = new Typecho_Widget_Helper_Form_Element_Text(
        'starttime', 
        NULL, 
        _t('2019-03-30'), 
        _t('网站建站时间'), 
        _t('在这里输入网站的创建/发布时间，格式为 YYYY-MM-DD（如 2019-03-30），用于在页脚动态计算并显示本站稳定运行的时长。')
    );
    $form->addInput($starttime);

    // 是否开启首页顶部幻灯片巨幕
    $showHeroSlider = new Typecho_Widget_Helper_Form_Element_Radio(
        'showHeroSlider',
        array('1' => _t('开启'), '0' => _t('关闭')),
        '1',
        _t('是否开启首页顶部幻灯片巨幕'),
        _t('选择是否在首页顶部展示高清摄影轮播巨幕。')
    );
    $form->addInput($showHeroSlider);

    // 自定义幻灯片列表
    $customHeroSlides = new Typecho_Widget_Helper_Form_Element_Textarea(
        'customHeroSlides',
        NULL,
        NULL,
        _t('自定义幻灯片列表'),
        _t('在这里自定义您的幻灯片。每一行代表一张幻灯片，格式为：<code>图片地址|分类|标题|作者|出处链接</code>。<br>
例如：<code>/usr/themes/pink-wall/assets/img/master/Ansel-Adams.jpg|风光类|优胜美地|Ansel Adams|https://anseladams.com</code><br>
留空则默认使用内置的 20 张经典摄影名作并随机循环。')
    );
    $form->addInput($customHeroSlides);
}

function get_post_image($archive) {
    // 1. 自定义字段 img
    if (isset($archive->fields->img) && !empty($archive->fields->img)) {
        return $archive->fields->img;
    }
    
    // 2. 第一张附件图片
    $db = Typecho_Db::get();
    $attach = $db->fetchRow($db->select()->from('table.contents')
        ->where('parent = ?', $archive->cid)
        ->where('type = ?', 'attachment')
        ->where('mime LIKE ?', 'image/%')
        ->limit(1));
    if ($attach) {
        $attach_text = json_decode($attach['text'], true);
        if (!$attach_text) {
            $attach_text = @unserialize($attach['text']);
        }
        if ($attach_text && isset($attach_text['path'])) {
            return Typecho_Common::url($attach_text['path'], Helper::options()->siteUrl);
        }
    }
    
    // 3. 内容中的第一张 img 标签
    preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $archive->content, $matches);
    if (isset($matches[1])) {
        return $matches[1];
    }
    
    // 4. 无图文章返回 null
    return null;
}

/**
 * 提取文章/附件的简短描述
 */
function get_post_desc($archive) {
    if (isset($archive->fields->description) && !empty($archive->fields->description)) {
        return $archive->fields->description;
    }
    // 检查附件本身的描述
    $db = Typecho_Db::get();
    $attach = $db->fetchRow($db->select()->from('table.contents')
        ->where('parent = ?', $archive->cid)
        ->where('type = ?', 'attachment')
        ->limit(1));
    if ($attach) {
        $attach_text = json_decode($attach['text'], true);
        if (!$attach_text) {
            $attach_text = @unserialize($attach['text']);
        }
        if ($attach_text && !empty($attach_text['description'])) {
            return $attach_text['description'];
        }
    }
    
    // 默认截取文章纯文本内容（避免调用会直接 echo 的 excerpt 方法）
    $text = !empty($archive->excerpt) ? $archive->excerpt : $archive->content;
    return Typecho\Common::subStr(strip_tags($text), 0, 60, '...');
}

/**
 * 提取封面关键词（自定义字段 keyword > 中文标题头2-4字 > 英文前1-2个单词）
 */
function get_post_keyword($archive) {
    // 1. 自定义字段 keyword
    if (isset($archive->fields->keyword) && !empty($archive->fields->keyword)) {
        return $archive->fields->keyword;
    }
    
    $title = trim($archive->title);
    // 去除常见标点符号
    $title_clean = preg_replace('/[[:punct:]]+/u', ' ', $title);
    
    // 2. 如果包含中文，截取前 2-4 个字
    if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $title_clean)) {
        $len = mb_strlen($title_clean, 'utf-8');
        if ($len <= 4) {
            return $title_clean;
        } else {
            // 优先截取前3个字以取得绝佳的垂直排版比例
            return mb_substr($title_clean, 0, min($len, 3), 'utf-8');
        }
    }
    
    // 3. 英文截取前 1 或 2 个单词
    $words = preg_split('/\s+/u', $title_clean);
    if (count($words) > 0 && !empty($words[0])) {
        return count($words) > 1 ? $words[0] . ' ' . $words[1] : $words[0];
    }
    
    return 'POST';
}

/**
 * 判断是否包含中文
 */
function is_chinese($str) {
    return preg_match('/[\x{4e00}-\x{9fa5}]/u', $str);
}

/**
 * 主题初始化：设置首页文章分页为极大值，平铺所有内容
 * 并支持获取大师幻灯片 AJAX 接口
 */
function themeInit($archive) {
    if ($archive->is('index')) {
        $archive->parameter->pageSize = 9999;
    }
    
    // 监听获取大师幻灯片的 API 请求
    if (isset($_GET['action']) && $_GET['action'] == 'get_master_slides') {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        
        $options = Helper::options();
        $slides = array();
        
        // 检查是否有自定义的幻灯片数据
        if (isset($options->customHeroSlides) && !empty(trim($options->customHeroSlides))) {
            $lines = explode("\n", $options->customHeroSlides);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                $parts = explode('|', $line);
                if (count($parts) >= 1 && !empty(trim($parts[0]))) {
                    $image = trim($parts[0]);
                    $category = isset($parts[1]) ? trim($parts[1]) : '默认';
                    $title = isset($parts[2]) ? trim($parts[2]) : '无标题';
                    $author = isset($parts[3]) ? trim($parts[3]) : '佚名';
                    $link = isset($parts[4]) ? trim($parts[4]) : '#';
                    
                    $slides[] = array(
                        'category' => $category,
                        'title' => $title,
                        'author' => $author,
                        'link' => $link,
                        'image' => $image
                    );
                }
            }
        }
        
        // 如果自定义列表解析为空，则回退使用内置的大师摄影作品
        if (empty($slides)) {
            $slides = get_master_slides();
        }
        
        shuffle($slides);
        // 随机截取 6 张以提高刷新随机感并显著降低移动端/网页加载的流量及资源占用
        $slides = array_slice($slides, 0, 6);
        echo json_encode($slides);
        exit;
    }
}

/**
 * 提取文章中的多张图片附件/链接（最多 4 张），用于画廊网格渲染
 */
function get_post_images($archive, $limit = 4) {
    $images = array();
    
    // 1. 自定义字段 img (支持逗号分隔多图)
    if (isset($archive->fields->img) && !empty($archive->fields->img)) {
        $custom_imgs = array_filter(array_map('trim', explode(',', $archive->fields->img)));
        foreach ($custom_imgs as $img) {
            if (count($images) >= $limit) break;
            $images[] = $img;
        }
    }
    
    // 2. 第一张附件图片
    if (count($images) < $limit) {
        $db = Typecho_Db::get();
        $attachments = $db->fetchAll($db->select()->from('table.contents')
            ->where('parent = ?', $archive->cid)
            ->where('type = ?', 'attachment')
            ->where('mime LIKE ?', 'image/%')
            ->order('order', Typecho_Db::SORT_ASC));
            
        foreach ($attachments as $attach) {
            if (count($images) >= $limit) break;
            $attach_text = json_decode($attach['text'], true);
            if (!$attach_text) {
                $attach_text = @unserialize($attach['text']);
            }
            if ($attach_text && isset($attach_text['path'])) {
                $url = Typecho_Common::url($attach_text['path'], Helper::options()->siteUrl);
                if (!in_array($url, $images)) {
                    $images[] = $url;
                }
            }
        }
    }
    
    // 3. 内容中的 img 标签
    if (count($images) < $limit) {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $archive->content, $matches);
        if (isset($matches[1])) {
            foreach ($matches[1] as $src) {
                if (count($images) >= $limit) break;
                if (!in_array($src, $images)) {
                    $images[] = $src;
                }
            }
        }
    }
    
    return $images;
}


/**
 * 获取大师摄影作品幻灯片列表（包含出处与高清无水印本地化图片）
 */
function get_master_slides() {
    $theme_url = Helper::options()->themeUrl;
    return array(
        array(
            'category' => '生活类',
            'title' => '1968年，南非，露天的人们',
            'author' => 'Ed van der Elsken',
            'link' => 'https://edvanderelsken.nl/',
            'image' => Typecho_Common::url('assets/img/master/Ed-van-der-Elsken.jpg', $theme_url)
        ),
        array(
            'category' => '人物类',
            'title' => '1963年，梵蒂冈，保罗六世加冕',
            'author' => 'Robert Lebeck',
            'link' => 'https://lebeck.de/reportage/italien/',
            'image' => Typecho_Common::url('assets/img/master/Robert-Lebeck.jpg', $theme_url)
        ),
        array(
            'category' => '新闻类',
            'title' => '1959年，苏联，赫鲁晓夫与尼克松',
            'author' => 'Elliott Erwitt',
            'link' => 'https://www.elliotterwitt.com/snaps',
            'image' => Typecho_Common::url('assets/img/master/Elliott-Erwitt.jpg', $theme_url)
        ),
        array(
            'category' => '纪实类',
            'title' => '1984年，巴基斯坦，阿富汗少女',
            'author' => 'Steve McCurry',
            'link' => 'https://www.stevemccurry.com/',
            'image' => Typecho_Common::url('assets/img/master/Steve-McCurry.jpg', $theme_url)
        ),
        array(
            'category' => '风景类',
            'title' => '2014年，俄罗斯，秋日之秋',
            'author' => 'Emil Gataullin',
            'link' => 'https://www.emilgataullin.com/',
            'image' => Typecho_Common::url('assets/img/master/Emil-Gataullin.jpg', $theme_url)
        ),
        array(
            'category' => '风光类',
            'title' => '1941年，美国，优胜美地谷',
            'author' => 'Ansel Adams',
            'link' => 'https://www.anseladams.com/',
            'image' => Typecho_Common::url('assets/img/master/Ansel-Adams.jpg', $theme_url)
        ),
        array(
            'category' => '纪实类',
            'title' => '1936年，美国，移民母亲',
            'author' => 'Dorothea Lange',
            'link' => 'https://www.loc.gov/exhibits/wcf/wcf02.html',
            'image' => Typecho_Common::url('assets/img/master/Dorothea-Lange.jpg', $theme_url)
        ),
        array(
            'category' => '纪实类',
            'title' => '1907年，美国，三等舱',
            'author' => 'Alfred Stieglitz',
            'link' => 'https://www.moma.org/collection/works/44627',
            'image' => Typecho_Common::url('assets/img/master/Alfred-Stieglitz.jpg', $theme_url)
        ),
        array(
            'category' => '工业类',
            'title' => '1920年，美国，发电厂技工',
            'author' => 'Lewis Hine',
            'link' => 'https://www.loc.gov/collections/national-child-labor-committee/about-this-collection/',
            'image' => Typecho_Common::url('assets/img/master/Lewis-Hine.jpg', $theme_url)
        ),
        array(
            'category' => '街头类',
            'title' => '1898年，法国，手风琴艺人',
            'author' => 'Eugène Atget',
            'link' => 'https://www.moma.org/artists/229',
            'image' => Typecho_Common::url('assets/img/master/Eugene-Atget.jpg', $theme_url)
        ),
        array(
            'category' => '肖像类',
            'title' => '1936年，美国，阿拉巴马农妇',
            'author' => 'Walker Evans',
            'link' => 'https://www.moma.org/artists/1777',
            'image' => Typecho_Common::url('assets/img/master/Walker-Evans.jpg', $theme_url)
        ),
        array(
            'category' => '劳动类',
            'title' => '1942年，美国，芝加哥铁路工人',
            'author' => 'Jack Delano',
            'link' => 'https://www.loc.gov/photos/?q=Jack+Delano',
            'image' => Typecho_Common::url('assets/img/master/Jack-Delano.jpg', $theme_url)
        ),
        array(
            'category' => '肖像类',
            'title' => '1942年，美国，美式哥特人像',
            'author' => 'Gordon Parks',
            'link' => 'https://www.gordonparksfoundation.org/',
            'image' => Typecho_Common::url('assets/img/master/Gordon-Parks.jpg', $theme_url)
        ),
        array(
            'category' => '风景类',
            'title' => '1866年，美国，优胜美地风光',
            'author' => 'Carleton Watkins',
            'link' => 'https://www.getty.edu/art/collection/artists/1932/carleton-watkins/',
            'image' => Typecho_Common::url('assets/img/master/Carleton-Watkins.jpg', $theme_url)
        ),
        array(
            'category' => '遗迹类',
            'title' => '1873年，美国，谢伊峡谷遗迹',
            'author' => 'Timothy O\'Sullivan',
            'link' => 'https://www.loc.gov/photos/?q=Timothy+O%27Sullivan',
            'image' => Typecho_Common::url('assets/img/master/Timothy-OSullivan.jpg', $theme_url)
        ),
        array(
            'category' => '历史类',
            'title' => '1905年，美国，杰罗尼莫肖像',
            'author' => 'Edward S. Curtis',
            'link' => 'https://www.edwardcurtis.com/',
            'image' => Typecho_Common::url('assets/img/master/Edward-S-Curtis.jpg', $theme_url)
        ),
        array(
            'category' => '科学类',
            'title' => '1878年，美国，奔跑中的马',
            'author' => 'Eadweard Muybridge',
            'link' => 'https://www.loc.gov/photos/?q=Eadweard+Muybridge',
            'image' => Typecho_Common::url('assets/img/master/Eadweard-Muybridge.jpg', $theme_url)
        ),
        array(
            'category' => '纪实类',
            'title' => '1888年，美国，纽约强盗窝街区',
            'author' => 'Jacob Riis',
            'link' => 'https://www.loc.gov/photos/?q=Jacob+Riis',
            'image' => Typecho_Common::url('assets/img/master/Jacob-Riis.jpg', $theme_url)
        ),
        array(
            'category' => '肖像类',
            'title' => '1867年，英国，托马斯·卡莱尔',
            'author' => 'Julia Margaret Cameron',
            'link' => 'https://www.moma.org/artists/937',
            'image' => Typecho_Common::url('assets/img/master/Julia-Margaret-Cameron.jpg', $theme_url)
        ),
        array(
            'category' => '艺术类',
            'title' => '1864年，法国，萨拉·伯恩哈特',
            'author' => 'Nadar',
            'link' => 'https://www.moma.org/artists/4196',
            'image' => Typecho_Common::url('assets/img/master/Nadar.jpg', $theme_url)
        )
    );
}

/**
 * 辅助函数：根据图片 URL 获取其在服务器上的物理路径
 */
function get_local_path_from_url($url) {
    if (empty($url)) {
        return null;
    }
    
    $options = Helper::options();
    $siteUrl = $options->siteUrl;
    
    // 规范化斜杠
    $siteUrl = rtrim($siteUrl, '/');
    
    $localPath = '';
    if (strpos($url, $siteUrl) === 0) {
        $relativePath = substr($url, strlen($siteUrl));
        $localPath = __TYPECHO_ROOT_DIR__ . '/' . ltrim($relativePath, '/');
    } elseif (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
        // 相对路径
        $localPath = __TYPECHO_ROOT_DIR__ . '/' . ltrim($url, '/');
    } else {
        // 外部链接，无法进行本地缩略图处理
        return null;
    }
    
    $localPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $localPath);
    if (file_exists($localPath)) {
        return $localPath;
    }
    return null;
}

/**
 * 动态裁剪/缩放图片并生成缩略图缓存，降低瀑布流网格的加载流量
 */
function get_image_thumb($url, $width = 600) {
    if (empty($url)) {
        return $url;
    }
    
    // 检查 GD 库是否启用
    if (!extension_loaded('gd')) {
        return $url;
    }
    
    $localPath = get_local_path_from_url($url);
    if (!$localPath) {
        return $url;
    }
    
    $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
    if ($ext === 'gif' || $ext === 'svg') {
        return $url; // gif/svg 原样返回
    }
    
    list($origWidth, $origHeight) = @getimagesize($localPath);
    if (!$origWidth || $origWidth <= $width) {
        return $url; // 尺寸已小于要求，或获取失败，原图返回
    }
    
    // 采用 MD5 计算缓存文件名，确保唯一性并规避中文乱码
    $cacheName = 'thumb_' . md5($localPath . '_' . $width) . '.' . $ext;
    $cacheDir = __TYPECHO_ROOT_DIR__ . '/usr/uploads/cache';
    $cacheFile = $cacheDir . '/' . $cacheName;
    
    // 检查缓存是否存在
    if (file_exists($cacheFile)) {
        return Typecho_Common::url('usr/uploads/cache/' . $cacheName, Helper::options()->siteUrl);
    }
    
    // 确保缓存目录存在
    if (!file_exists($cacheDir)) {
        @mkdir($cacheDir, 0777, true);
    }
    
    $ratio = $origHeight / $origWidth;
    $newWidth = $width;
    $newHeight = (int)($newWidth * $ratio);
    
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $srcImg = @imagecreatefromjpeg($localPath);
            break;
        case 'png':
            $srcImg = @imagecreatefrompng($localPath);
            break;
        case 'webp':
            $srcImg = @imagecreatefromwebp($localPath);
            break;
        default:
            return $url;
    }
    
    if (!$srcImg) {
        return $url;
    }
    
    $dstImg = imagecreatetruecolor($newWidth, $newHeight);
    
    // 保持透明通道 (PNG & WebP)
    if ($ext === 'png' || $ext === 'webp') {
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);
        if ($ext === 'png') {
            $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
            imagefilledrectangle($dstImg, 0, 0, $newWidth, $newHeight, $transparent);
        }
    }
    
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
    
    $success = false;
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $success = @imagejpeg($dstImg, $cacheFile, 85);
            break;
        case 'png':
            $success = @imagepng($dstImg, $cacheFile, 6);
            break;
        case 'webp':
            $success = @imagewebp($dstImg, $cacheFile, 80);
            break;
    }
    
    imagedestroy($srcImg);
    imagedestroy($dstImg);
    
    if ($success) {
        return Typecho_Common::url('usr/uploads/cache/' . $cacheName, Helper::options()->siteUrl);
    }
    
    return $url;
}


