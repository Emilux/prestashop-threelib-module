<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
<script type="text/javascript">
    let imageSlide = document.querySelectorAll('.product-image-show')
    let modelPreview = document.querySelector('#models-preview')
    modelPreview.addEventListener('click', function (e) {
        e.preventDefault();
        console.log('hookhoeazezazeak')
        let moduleShow = document.querySelector('#product-models-3d')
        let productImageHide = document.querySelector('.product-cover-hide')

        if (this.dataset.models){
            productImageHide.style.display = "none"
            moduleShow.innerHTML = "<model-viewer  id='preview-field' src='"+this.dataset.models+"' alt='A 3D model of an astronaut' ar ar-modes='webxr scene-viewer quick-look' environment-image='neutral' auto-rotate camera-controls>"
        }

    })
    imageSlide.forEach( function (e) {
            e.addEventListener('click', function (e) {
                e.preventDefault();
                console.log("autre")
                let previewField = document.querySelector('#preview-field')
                let productImageHide = document.querySelector('.product-cover-hide')
                if (previewField)
                    previewField.remove();
                productImageHide.style.display = "initial"
            })
        }
    );
    console.log('hookhok')
</script>