export default class ImagePreviewCompress{
    static init(){
        this.preview();
    }
    static preview(){
        let compressBtn = document.getElementById('compressImage');

        let fileId = compressBtn.dataset.fileId;
        let img = document.getElementById("previewCompressedImage");
        console.log(img)

        compressBtn.addEventListener('click', function(){
            let value = document.getElementById('image_compress_quality').value;
            console.log(value)
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "/admin/compress-preview/" + fileId)
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function(){
                console.log(xhr.response)
                console.log(JSON.parse(xhr.response))
                let response = JSON.parse(xhr.response);
                if(response.error){
                    img.after('p', response.error);
                }else if(response.path){
                    
                    img.setAttribute("src", response.path);
                }
            }
            xhr.send('quality='+value)
        })
    }
}