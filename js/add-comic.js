/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function($){

    $('a.wp-post-thumbnail').addClass('hidden');

    $('.manga-press-add-comic-link').on('click', function(e){
        e.preventDefault();
        
        var nonce = $(this).attr('data-nonce'),
            attachment_id = $(this).attr('data-attachment-id'),        
            post_parent   = $(this).attr('data-post-parent'),
            data = {
                'nonce' : nonce,
                'attachment_id' : attachment_id,
                'action'        : 'add-comic',
                'post_parent'   : post_parent
            };
            
        _get_comic_html(data);
        
    });
    
    $('#comic-image .inside').on('click', '#remove-comic-thumbnail', function(e){
        e.preventDefault();

        var nonce = $(this).attr('data-nonce'),
            post_parent   = $(this).attr('data-post-parent'),
            data = {
                'nonce' : nonce,
                'attachment_id' : -1,
                'action'        : 'remove-comic',
                'post_parent'   : post_parent
            };

        _get_comic_html(data);
       
    });
    
    var _get_comic_html = function(input_data) {
        
        $.ajax({
            url : ajaxurl,
            data : input_data,
            type : "POST",
            success : function(data) {
                
                if (typeof(data.error) == 'undefined' || typeof(data.html) !== 'undefined')
                    $('#comic-image .inside', window.parent.document).html(data.html)
                
                self.parent.tb_remove();                
            },
            
            error : function(jqXHR, textStatus, errorThrown){
                console.log(jqXHR, textStatus, errorThrown);
            }
            
        });
        
    }
    
});
