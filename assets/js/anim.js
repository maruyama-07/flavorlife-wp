document.addEventListener("DOMContentLoaded", () => {
    if (typeof gsap === "undefined") return;
    if (typeof ScrollTrigger === "undefined") return;
  
    gsap.registerPlugin(ScrollTrigger);
  
    document.querySelectorAll(".js-animate-content").forEach((wrap) => {
      const targets = wrap.querySelectorAll("h2, h3, p, li, figure, img, .c-custom-button, .p-top-mv-topics ");
  
      gsap.fromTo(
        targets,
        { autoAlpha: 0, y: 12 },
        {
          autoAlpha: 1,
          y: 0,
          duration: 0.6,
          ease: "power2.out",
          stagger: 0.06,
          scrollTrigger: {
            trigger: wrap,
            start: "top 60%",
          },
        }
      );
    });
  });

  document.addEventListener("DOMContentLoaded", () => {
    if (typeof gsap === "undefined") return;
  
    gsap.fromTo(
      ".splide__slide-text",
      { autoAlpha: 0, },
      {
        autoAlpha: 1,
        y: 0,
        duration: 0.4,
        ease: "power2.out",
        delay: 0.2
      }
    );
  });