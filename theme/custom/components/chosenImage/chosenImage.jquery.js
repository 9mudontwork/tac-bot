/*
 * Chosen jQuery plugin to add an image to the dropdown items.
 */
(function($) {
    $.fn.chosenImage = function(options) {
        return this.each(function() {
            var $select = $(this);
            var questData  = {};
            var oldText = {};
            var setData = 0;

            // 1. Retrieve img-src from data attribute and build object of image sources for each list item.
            $select.find('option').filter(function(){
                return $(this).text();
            }).each(function(i) {
                // questData[i] = $(this).attr('data-img-src');
                questData[i] = $(this).attr('quest-data');
                oldText[i]= $(this).text();
            });

            

            // 2. Execute chosen plugin and get the newly created chosen container.
            $select.chosen(options);
            var $chosen = $select.next('.chosen-container').addClass('chosenImage-container');

            // 3. Style lis with image sources.
            $chosen.on('click.chosen, mousedown.chosen, keyup.chosen', function(event){
                $chosen.find('.chosen-results li').each(function() {
                    var index = $(this).attr('data-option-array-index');
                    $(this).html(cssObj(oldText[index], questData[index]));
                    setData = 1;
                });
            });

            // 4. Change image on chosen selected element when form changes.
            $select.change(function() {
                // var imgSrc = $select.find('option:selected').attr('data-img-src') || '';
                var imgSrc = $select.find('option:selected').attr('quest-data') || '';
                var text = $select.find('option:selected').text();
                $chosen.find('.chosen-single span').html(cssObj(text, imgSrc));
            });
            $select.trigger('change');

            // Utilties
            function cssObj(oldText, questData) {
                if(questData){
                    var questDataJson = JSON.parse(questData);
                    var html = oldText;
                    html = html + ' = ';
                    $.each(questDataJson, function(i, item) {
                        // html = html + item.name + ': <img style="heigth:35px; width:35px;" src="http://cdn.alchemistcodedb.com/images/items/icons/' + item.url + '">';
                        html = html + ' <img style="heigth:35px; width:35px;" src="http://cdn.alchemistcodedb.com/images/items/icons/' + item.url + '">';
                    });
                    return html;
                }
                
            }
        });
    };
})(jQuery);
