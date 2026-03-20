export default class ShowContactFooter{
    static init(){
        this.show();
    }

    static show() {
        let midwifeContact = document.getElementsByClassName('footer-midwife-name');
        [...midwifeContact].forEach(midwife=>midwife.addEventListener("click", function(e){
            let target = e.currentTarget;
            let contactBox = target.nextElementSibling;
            contactBox.classList.toggle("display");
            let  contactBoxes = document.getElementsByClassName('footer-midwife-contact');
            [...contactBoxes].forEach(box=> box !== contactBox ? box.classList.remove('display') : null)
        }))
    }
}