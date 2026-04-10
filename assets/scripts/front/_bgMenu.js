class backgroundMenu {
  static init() {
    this.background();
  }
  static background() {
    let header;
    let sentinel;
    sentinel = document.querySelector('#sentinel');
    header = document.querySelector('header');
    let x = window.matchMedia("(max-width: 768px)");
    if (x.matches) { // If media query matches
      header.classList.remove("is-sticky");
    }else{
      createObserver();
    }

    function createObserver() {
      let observer;
      let options = {};
      observer = new IntersectionObserver(handleIntersect, options);
      observer.observe(sentinel);
    }
    function handleIntersect (entries) {
      entries.forEach(function(entry){
        if (entry.intersectionRatio === 0) {
          header.classList.add("is-sticky");
        } else if (entry.intersectionRatio === 1) {
          header.classList.remove("is-sticky");
        }
      })
    }
  }
}
export default backgroundMenu
