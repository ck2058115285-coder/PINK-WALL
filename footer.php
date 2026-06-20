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

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>. All Rights Reserved.</p>
        <p class="site-run-time">本站已稳定运行: <span id="run-time-display" style="font-weight: 500;">0 天 0 小时 0 分 0 秒</span></p>
        <p>Powered by <a href="http://typecho.org" target="_blank" rel="nofollow">Typecho</a>. Theme: <a href="mailto:ck2058115285@gmail.com" target="_blank">PINK WALL</a> by RK.</p>
    </div>
</footer>

<!-- 内置图片放大浏览器 (Lightbox) HTML 骨架 -->
<div id="lightbox" class="lightbox" aria-label="Image Viewer" role="dialog">
    <!-- Lightbox 翻页按钮 -->
    <button class="lightbox-arrow prev-btn" id="lightbox-prev" aria-label="Previous Image">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>
    <button class="lightbox-arrow next-btn" id="lightbox-next" aria-label="Next Image">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </button>
    
    <div class="lightbox-content">
        <img id="lightbox-img" class="lightbox-img" src="" alt="Zoomed view">
        <div class="lightbox-caption">
            <h2 id="lightbox-title" class="lightbox-title"></h2>
            <p id="lightbox-desc" class="lightbox-desc"></p>
        </div>
    </div>
</div>

<!-- Typecho 系统底部挂载 -->
<?php $this->footer(); ?>

<!-- 核心日夜模式、惰性加载及灯箱弹出 JS 交互 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === 1. 日夜模式切换控制 ===
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const targetTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', targetTheme);
            localStorage.setItem('theme', targetTheme);
        });
    }

    // === 2. 内置大图浏览器 (Lightbox) ===
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxTitle = document.getElementById('lightbox-title');
    const lightboxDesc = document.getElementById('lightbox-desc');
    const cardLinks = document.querySelectorAll('.card-link');
    
    let currentGalleryLinks = [];
    let currentGalleryIndex = 0;

    cardLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // 查找同一个卡片 (例如同一个 grid-item 或 gallery-card-wrapper) 中的所有图片链接
            const parentCard = this.closest('.grid-item') || this.closest('.gallery-card-wrapper');
            if (parentCard) {
                currentGalleryLinks = Array.from(parentCard.querySelectorAll('.card-link'));
            } else {
                currentGalleryLinks = [this];
            }
            
            currentGalleryIndex = currentGalleryLinks.indexOf(this);
            if (currentGalleryIndex === -1) {
                currentGalleryIndex = 0;
            }
            
            openLightbox();
        });
    });

    function openLightbox() {
        if (!lightbox || !lightboxImg) return;
        
        updateLightboxContent();
        
        lightbox.style.display = 'flex';
        setTimeout(() => {
            lightbox.classList.add('active');
        }, 10);
        
        // 如果图集图片大于1张，显示翻页箭头；否则隐藏
        const prevBtn = document.getElementById('lightbox-prev');
        const nextBtn = document.getElementById('lightbox-next');
        if (prevBtn && nextBtn) {
            if (currentGalleryLinks.length > 1) {
                prevBtn.style.display = 'flex';
                nextBtn.style.display = 'flex';
            } else {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            }
        }
    }

    function updateLightboxContent() {
        const link = currentGalleryLinks[currentGalleryIndex];
        if (link && lightboxImg) {
            lightboxImg.src = link.getAttribute('href');
            lightboxTitle.textContent = link.getAttribute('data-title') || '';
            lightboxDesc.textContent = link.getAttribute('data-desc') || '';
        }
    }

    function prevImage() {
        if (currentGalleryLinks.length <= 1) return;
        currentGalleryIndex = (currentGalleryIndex - 1 + currentGalleryLinks.length) % currentGalleryLinks.length;
        updateLightboxContent();
    }

    function nextImage() {
        if (currentGalleryLinks.length <= 1) return;
        currentGalleryIndex = (currentGalleryIndex + 1) % currentGalleryLinks.length;
        updateLightboxContent();
    }

    // 绑定左右切换按钮的点击事件
    const lPrevBtn = document.getElementById('lightbox-prev');
    const lNextBtn = document.getElementById('lightbox-next');
    if (lPrevBtn) {
        lPrevBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // 阻止事件冒泡防止关闭弹窗
            prevImage();
        });
    }
    if (lNextBtn) {
        lNextBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // 阻止事件冒泡防止关闭弹窗
            nextImage();
        });
    }

    function closeLightbox() {
        if (lightbox) {
            lightbox.classList.remove('active');
            setTimeout(() => {
                lightbox.style.display = 'none';
                if (lightboxImg) lightboxImg.src = '';
            }, 300);
        }
    }

    if (lightbox) {
        lightbox.addEventListener('click', function(e) {
            // 点击外围或者图片背景时关闭
            closeLightbox();
        });
    }

    // 监听键盘方向键翻页
    document.addEventListener('keydown', function(e) {
        if (lightbox && lightbox.classList.contains('active')) {
            if (e.key === 'ArrowLeft') {
                prevImage();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            } else if (e.key === 'Escape') {
                closeLightbox();
            }
        }
    });

    // === 3. 图片惰性加载 (Lazy Load) 与渐显过渡 ===
    const lazyImages = document.querySelectorAll('img.lazy');
    
    // 给所有惰性图片设置初始状态样式
    lazyImages.forEach(img => {
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.6s ease';
    });

    if ('IntersectionObserver' in window) {
        const lazyImageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const lazyImage = entry.target;
                    // 先绑定 onload / onerror 事件，再给 src 赋值以防止浏览器缓存导致事件丢失
                    lazyImage.onload = function() {
                        lazyImage.style.opacity = '1';
                        const wrapper = lazyImage.closest('.card-img-wrapper');
                        if (wrapper) {
                            wrapper.classList.add('loaded');
                        }
                    };
                    lazyImage.onerror = function() {
                        lazyImage.style.opacity = '1';
                        const wrapper = lazyImage.closest('.card-img-wrapper');
                        if (wrapper) {
                            wrapper.classList.add('loaded', 'load-error');
                        }
                    };
                    lazyImage.src = lazyImage.dataset.src;
                    lazyImageObserver.unobserve(lazyImage);
                }
            });
        });

        lazyImages.forEach(function(lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });
    } else {
        // 浏览器不支持观察器时的降级 fallback
        lazyImages.forEach(img => {
            img.onload = function() {
                img.style.opacity = '1';
                const wrapper = img.closest('.card-img-wrapper');
                if (wrapper) {
                    wrapper.classList.add('loaded');
                }
            };
            img.onerror = function() {
                img.style.opacity = '1';
                const wrapper = img.closest('.card-img-wrapper');
                if (wrapper) {
                    wrapper.classList.add('loaded', 'load-error');
                }
            };
            img.src = img.dataset.src;
        });
    }

    // === 4. 网站运行时间动态计算 ===
    const runTimeDisplay = document.getElementById('run-time-display');
    if (runTimeDisplay) {
        const startTimeStr = "<?php echo $this->options->starttime ? $this->options->starttime : '2019-03-30'; ?>";
        // 兼容斜杠格式并安全解析
        const startTime = new Date(startTimeStr.replace(/-/g, '/')).getTime();
        
        function updateRunTime() {
            const now = new Date().getTime();
            const diff = now - startTime;
            
            if (diff < 0) {
                runTimeDisplay.textContent = "刚刚诞生";
                return;
            }
            
            const msPerSecond = 1000;
            const msPerMinute = msPerSecond * 60;
            const msPerHour = msPerMinute * 60;
            const msPerDay = msPerHour * 24;
            
            const days = Math.floor(diff / msPerDay);
            const hours = Math.floor((diff % msPerDay) / msPerHour);
            const minutes = Math.floor((diff % msPerHour) / msPerMinute);
            const seconds = Math.floor((diff % msPerMinute) / msPerSecond);
            
            runTimeDisplay.textContent = `${days} 天 ${hours} 小时 ${minutes} 分 ${seconds} 秒`;
        }
        
        updateRunTime();
        setInterval(updateRunTime, 1000);
    }

    // === 5. 首页顶部巨幕幻灯片轮播逻辑 ===
    const heroSlider = document.getElementById('hero-slider');
    const dotsContainer = document.getElementById('slider-dots');
    const prevArrow = document.getElementById('prev-arrow');
    const nextArrow = document.getElementById('next-arrow');
    const scrollDownBtn = document.getElementById('scroll-down');
    
    if (heroSlider && dotsContainer) {
        const apiUrl = '<?php $this->options->siteUrl(); ?>index.php?action=get_master_slides&t=' + Date.now();
        
        fetch(apiUrl)
            .then(response => response.json())
            .then(slides => {
                if (!slides || slides.length === 0) {
                    throw new Error('No slides data found.');
                }
                
                // 清空 loading 状态
                heroSlider.innerHTML = '';
                dotsContainer.innerHTML = '';
                
                // 动态构建 HTML
                slides.forEach((slide, index) => {
                    // 幻灯片项
                    const slideItem = document.createElement('div');
                    slideItem.className = `slide-item ${index === 0 ? 'active' : ''}`;
                    slideItem.style.backgroundImage = `url('${slide.image}')`;
                    
                    const overlay = document.createElement('div');
                    overlay.className = 'slide-overlay';
                    
                    const infoClean = document.createElement('div');
                    infoClean.className = 'slide-info-clean container';
                    
                    const descText = document.createElement('div');
                    descText.className = 'slide-description-text';
                    
                    const catBadgeP = document.createElement('p');
                    const catBadge = document.createElement('span');
                    catBadge.className = 'slide-cat-badge';
                    catBadge.textContent = slide.category;
                    catBadgeP.appendChild(catBadge);
                    
                    const titleP = document.createElement('p');
                    titleP.innerHTML = `${escapeHtml(slide.title)}。by <a href="${escapeHtml(slide.link)}" target="_blank" rel="noopener noreferrer">${escapeHtml(slide.author)}</a>`;
                    
                    descText.appendChild(catBadgeP);
                    descText.appendChild(titleP);
                    infoClean.appendChild(descText);
                    slideItem.appendChild(overlay);
                    slideItem.appendChild(infoClean);
                    heroSlider.appendChild(slideItem);
                    
                    // 控制点
                    const dot = document.createElement('span');
                    dot.className = `slider-dot ${index === 0 ? 'active' : ''}`;
                    dot.setAttribute('data-slide', index);
                    dotsContainer.appendChild(dot);
                });
                
                // 显示翻页箭头
                if (prevArrow) prevArrow.style.display = 'flex';
                if (nextArrow) nextArrow.style.display = 'flex';
                
                // 初始化轮播事件绑定
                initSlider(slides.length);
            })
            .catch(err => {
                console.error('Failed to load slides:', err);
                heroSlider.innerHTML = '<div class="slide-item active" style="background: #111; display: flex; align-items: center; justify-content: center; height: 100%;"><div style="color: rgba(255,255,255,0.6); font-size: 1rem;">加载失败，请刷新重试</div></div>';
            });
            
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;');
        }
        
        function initSlider(slidesCount) {
            const slideItems = heroSlider.querySelectorAll('.slide-item');
            const dots = dotsContainer.querySelectorAll('.slider-dot');
            let currentSlide = 0;
            let slideInterval = setInterval(nextSlide, 5000);
            
            function showSlide(index) {
                slideItems.forEach(item => item.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));
                
                slideItems[index].classList.add('active');
                dots[index].classList.add('active');
                currentSlide = index;
            }
            
            function nextSlide() {
                let next = (currentSlide + 1) % slidesCount;
                showSlide(next);
            }
            
            dots.forEach(dot => {
                dot.addEventListener('click', function() {
                    clearInterval(slideInterval);
                    const idx = parseInt(this.getAttribute('data-slide'));
                    showSlide(idx);
                    slideInterval = setInterval(nextSlide, 5000);
                });
            });
            
            if (prevArrow) {
                prevArrow.addEventListener('click', function() {
                    clearInterval(slideInterval);
                    let prev = (currentSlide - 1 + slidesCount) % slidesCount;
                    showSlide(prev);
                    slideInterval = setInterval(nextSlide, 5000);
                });
            }
            
            if (nextArrow) {
                nextArrow.addEventListener('click', function() {
                    clearInterval(slideInterval);
                    nextSlide();
                    slideInterval = setInterval(nextSlide, 5000);
                });
            }
        }
        
        if (scrollDownBtn) {
            scrollDownBtn.addEventListener('click', function() {
                const intro = document.querySelector('.site-intro');
                if (intro) {
                    intro.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    }
});
</script>
</body>
</html>
