<script type="text/javascript" src="jquery.slicebox.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var Page = (function() {

            var $navArrows = $('#nav-arrows').hide(),
                    $shadow = $('#shadow').hide(),
                    slicebox = $('#sb-slider').slicebox({
                onReady: function() {

                    $navArrows.show();
                    $shadow.show();

                },
                orientation: 'v',
                colorHiddenSides : 'transparent',
                cuboidsRandom: true,
                disperseFactor: 20,
                perspective         : 1200,    
                autoplay: true,
                interval: 8000
            }),
            init = function() {

                initEvents();

            },
                    initEvents = function() {

                // add navigation events
                $navArrows.children(':first').on('click', function() {

                    slicebox.next();
                    return false;

                });

                $navArrows.children(':last').on('click', function() {

                    slicebox.previous();
                    return false;

                });

            };

            return {init: init};

        })();

        Page.init();


    });




</script>