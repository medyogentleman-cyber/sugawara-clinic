document.addEventListener('DOMContentLoaded', () => {
  // 1. Hero Slider
  const slides = document.querySelectorAll('.hero-slide');
  let currentSlide = 0;

  function nextSlide() {
    slides[currentSlide].classList.remove('active');
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add('active');
  }
  // 初期表示
  if (slides.length > 0) {
    slides[0].classList.add('active');
    setInterval(nextSlide, 5000); // 5秒ごとにスライド
  }

  // 2. Scroll Animation (Fade In)
  const fadeElements = document.querySelectorAll('.fade-in');
  
  const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('appear');
        fadeObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  fadeElements.forEach(el => fadeObserver.observe(el));

  // 3. Floating CTA 
  const floatingCta = document.getElementById('floating-cta');
  const heroSection = document.querySelector('.hero');
  
  window.addEventListener('scroll', () => {
    if (window.scrollY > (heroSection.offsetHeight / 2)) {
      floatingCta.classList.add('visible');
    } else {
      floatingCta.classList.remove('visible');
    }
  });

  // 4. Smooth Scroll for Anchor Links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId !== '#') {
        e.preventDefault();
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          const headerOffset = 70;
          const elementPosition = targetElement.getBoundingClientRect().top;
          const offsetPosition = elementPosition + window.scrollY - headerOffset;

          window.scrollTo({
            top: offsetPosition,
            behavior: "smooth"
          });
        }
      }
    });
  });

  // 5. Ajax Form Submission
  const contactForm = document.getElementById('contact-form');
  const formMessage = document.getElementById('form-message');
  const submitBtn = document.getElementById('submit-btn');

  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      
      submitBtn.disabled = true;
      submitBtn.innerText = '送信中...';
      formMessage.style.display = 'none';
      formMessage.className = '';

      // PHPを使わないダミーの送信処理 (1.5秒後に成功メッセージを表示)
      setTimeout(() => {
        formMessage.innerText = '【テスト送信】お問い合わせを受け付けました。担当者からの連絡をお待ちください。';
        formMessage.classList.add('msg-success');
        formMessage.style.display = 'block';
        contactForm.reset();
        
        submitBtn.disabled = false;
        submitBtn.innerText = '送信する';
      }, 1500);
    });
  }
});
