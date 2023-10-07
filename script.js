document.addEventListener('DOMContentLoaded', function() {
    const pA = document.querySelector('.a');
    const pB = document.querySelector('.b');

    adjustFontSize(pA, pB);
});

function adjustFontSize(pA, pB) {
    const targetWidth = pA.offsetWidth;
    let fontSize = 30; 
    pB.style.fontSize = `${fontSize}px`;

    while (pB.offsetWidth > targetWidth && fontSize > 5) {
        fontSize -= 0.5;
        pB.style.fontSize = `${fontSize}px`;
    }
}
