// ========================================
// MOLNÁR BARBER SHOP - CLEAN & SIMPLE JS
// ========================================

document.addEventListener('DOMContentLoaded', function () {

    // ========================================
    // MOBILE MENU
    // ========================================
    const menuBtn = document.querySelector('.menu-btn');
    const menu = document.querySelector('.menu');
    const menuLinks = document.querySelectorAll('.menu a');

    if (menuBtn && menu) {
        menuBtn.addEventListener('click', function () {
            menuBtn.classList.toggle('active');
            menu.classList.toggle('active');
        });

        menuLinks.forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    menuBtn.classList.remove('active');
                    menu.classList.remove('active');
                }
            });
        });
    }

    // ========================================
    // SMOOTH SCROLL
    // ========================================
    const scrollLinks = document.querySelectorAll('a[href^="#"]');

    scrollLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ========================================
    // ACTIVE SECTION HIGHLIGHT
    // ========================================
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.menu a[href^="#"]');

    function highlightActiveSection() {
        let currentSection = '';
        const scrollPos = window.scrollY + window.innerHeight / 2;

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;

            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                currentSection = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + currentSection) {
                link.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', highlightActiveSection);
    highlightActiveSection();

    // ========================================
    // SCROLL PROGRESS BAR
    // ========================================
    const progress = document.querySelector('.progress');

    if (progress) {
        window.addEventListener('scroll', function () {
            const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (window.scrollY / windowHeight) * 100;
            progress.style.width = scrolled + '%';
        });
    }

    // ========================================
    // AOS ANIMATION
    // ========================================
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100,
            easing: 'ease-in-out'
        });
    }

    // ========================================
    // GLIGHTBOX GALLERY
    // ========================================
    if (typeof GLightbox !== 'undefined') {
        GLightbox({
            touchNavigation: true,
            loop: true,
            autoplayVideos: true,
            closeButton: true,
            zoomable: false
        });
    }

    // ========================================
    // SZOLGÁLTATÁSOK RÉSZLETEK MENÜ
    // ========================================
    const modal = document.querySelector('.modal');
    const modalBody = document.querySelector('.modal-body');
    const modalClose = document.querySelector('.modal-close');
    const detailBtns = document.querySelectorAll('.btn-details');

    const serviceData = {
        haircut: {
            title: 'Hajvágás',
            description: 'Modern és klasszikus hajvágási technikák minden korosztálynak. Professzionális borbélyok, akik tökéletesen megértik az igényeidet.',
            features: [
                'Személyre szabott konzultáció',
                'Modern fazonok és klasszikus stílusok',
                'Hajmosás és hajszárítás',
                'Styling tanácsok'
            ],
            price: 'Ár: 4.500 - 6.500 Ft'
        },
        beard: {
            title: 'Szakáll Ápolás',
            description: 'Teljes körű szakáll ápolás, formázás, borotválás klasszikus stílusban. Prémium termékekkel.',
            features: [
                'Szakáll trimmelés és formázás',
                'Klasszikus borotválás',
                'Meleg törölköző kezelés',
                'Szakáll ápoló termékek'
            ],
            price: 'Ár: 3.500 - 5.500 Ft'
        },
        facial: {
            title: 'Arckezelés',
            description: 'Frissítő és relaxáló arckezelések férfiaknak. Tisztító, hidratáló és revitalizáló kezelések.',
            features: [
                'Mélytisztító arckezelés',
                'Masszázs és relaxáció',
                'Hidratáló maszk',
                'Bőrápoló tanácsadás'
            ],
            price: 'Ár: 6.000 - 8.500 Ft'
        },
        styling: {
            title: 'Hajstyling',
            description: 'Professzionális hajstyling szolgáltatások különleges alkalmakra vagy mindennapi használatra.',
            features: [
                'Alkalmi hajstyling',
                'Mindennapi styling technikák',
                'Termék ajánlások',
                'Styling tanácsadás'
            ],
            price: 'Ár: 3.000 - 5.000 Ft'
        },
        coloring: {
            title: 'Festés & Melír',
            description: 'Professzionális hajfestés, szőkítés és melír szolgáltatások prémium minőségű termékekkel.',
            features: [
                'Teljes hajfestés',
                'Melír és ombre technikák',
                'Ősz hajszálak lefedése',
                'Színtanácsadás'
            ],
            price: 'Ár: 8.000 - 15.000 Ft'
        }
    };

    detailBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const serviceType = this.getAttribute('data-service');
            const service = serviceData[serviceType];

            if (service && modal && modalBody) {
                let featuresHTML = service.features.map(f => `<li>${f}</li>`).join('');

                modalBody.innerHTML = `
                    <h2>${service.title}</h2>
                    <p>${service.description}</p>
                    <ul>${featuresHTML}</ul>
                    <p><strong>${service.price}</strong></p>
                    <a href="appointments.php" class="btn-primary" style="margin-top: 20px;">Foglalj időpontot</a>
                `;

                modal.classList.add('active');
            }
        });
    });

    if (modalClose) {
        modalClose.addEventListener('click', function () {
            modal.classList.remove('active');
        });
    }

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    }

    // ========================================
    // FAQ ACCORDION
    // ========================================
    const faqBtns = document.querySelectorAll('.faq-btn');

    faqBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const article = this.parentElement;
            const isActive = article.classList.contains('active');

            document.querySelectorAll('.faq-list article').forEach(item => {
                item.classList.remove('active');
            });

            if (!isActive) {
                article.classList.add('active');
            }
        });
    });

    // ========================================
    // SCROLL DOWN INDICATOR
    // ========================================
    const scrollDown = document.querySelector('.scroll-down');

    if (scrollDown) {
        scrollDown.addEventListener('click', function () {
            const aboutSection = document.getElementById('about');
            if (aboutSection) {
                aboutSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // ========================================
    // TEAM GRID CAROUSEL
    // ========================================
    const csapat = [
        {
            img: "../images/hairdressers/molnar_istvan.jpg",
            nev: "Molnár István",
            poz: "Főborbély, szakmai vezető",
            bio: "15 év tapasztalat, klasszikus technikák specialistája",
            link: "appointments.php?barber=1"
        },
        {
            img: "../images/hairdressers/kiss_gabor.jpg",
            nev: "Kiss Gábor",
            poz: "Borbélymester",
            bio: "Modern stílusok és fade technikák mestere",
            link: "appointments.php?barber=2"
        },
        {
            img: "../images/hairdressers/nagy_peter.jpg",
            nev: "Nagy Péter",
            poz: "Borbély",
            bio: "Szakáll styling és arcápolás szakértő",
            link: "appointments.php?barber=3"
        },
        {
            img: "../images/hairdressers/szabo_andras.jpg",
            nev: "Szabó András",
            poz: "Senior borbély",
            bio: "Gyerekek, tinédzserek kedvence, 10 év tapasztalat",
            link: "appointments.php?barber=4"
        },
        {
            img: "../images/hairdressers/kiss_andrea.jpg",
            nev: "Kiss Andrea",
            poz: "Borbély asszisztens",
            bio: "Fiatal tehetség, modern trendek követője",
            link: "appointments.php?barber=5"
        }
    ];
    const track = document.querySelector('.carousel-track');
    const leftBtn = document.querySelector('.carousel-arrow.left');
    const rightBtn = document.querySelector('.carousel-arrow.right');
    const visible = 3;
    let pos = 0;

    function renderCarousel() {
        track.innerHTML = '';
        for (let i = 0; i < visible; i++) {
            const idx = (pos + i) % csapat.length;
            const tag = csapat[idx];
            track.innerHTML += `
        <article>
          <img src="${tag.img}" alt="${tag.nev}">
          <h3>${tag.nev}</h3>
          <p>${tag.poz}</p>
          <p class="bio">${tag.bio}</p>
          <a href="${tag.link}" class="btn-small">Foglalás hozzá</a>
        </article>
      `;
        }
    }

    leftBtn.addEventListener('click', function () {
        pos = (pos - 1 + csapat.length) % csapat.length;
        renderCarousel();
    });

    rightBtn.addEventListener('click', function () {
        pos = (pos + 1) % csapat.length;
        renderCarousel();
    });

    renderCarousel();
});