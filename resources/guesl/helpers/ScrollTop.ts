const ScrollTop = {
    init: function (idSelector: string) {
        // Back to up function
        let backToUpButton = document.getElementById(idSelector);
        if (backToUpButton) {
            // When the user scrolls down 20px from the top of the document, show the button
            window.onscroll = function () {
                scrollFunction()
            };

            backToUpButton.addEventListener('click', function () {
                topFunction();
            });
        }

        const scrollFunction = () => {
            if (backToUpButton) {
                if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                    backToUpButton.style.display = 'block';
                    backToUpButton.style.opacity = '1';
                } else {
                    backToUpButton.style.display = 'none';
                    backToUpButton.style.opacity = '0';
                }
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        const topFunction = () => {
            const body = document.querySelector('body') as HTMLBodyElement;
            const html = document.querySelector('html') as HTMLElement;

            body.animate({
                scrollTop: body.offsetTop - 75
            }, 600);

            html.animate({
                scrollTop: body.offsetTop - 75
            }, 600);
        }
    }
}

export default ScrollTop;
