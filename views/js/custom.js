let modelPreview = document.querySelector('#models-preview')
modelPreview.addEventListener('click', function (e) {
    e.preventDefault();
    console.log('hookhoeazezazeak')
    let product = document.querySelector('.product-cover')
    product.innerHTML = '<model-viewer src="https://modelviewer.dev/shared-assets/models/Astronaut.glb" alt="A 3D model of an astronaut" ar ar-modes="webxr scene-viewer quick-look" environment-image="neutral" auto-rotate camera-controls><p>test</p>'

})
console.log('hookhok')
