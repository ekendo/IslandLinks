<div style="width:100%;padding: 0px 3px 3px 3px;">
    <div style="float:left;width:20%;">
        <script type="text/javascript">//<![CDATA[
        document.write('<a href="javascript:void window.open(\'{image_url}product/{product_full_image}\', \'win2\', \'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width={full_image_width},height={full_image_height},directories=no,location=no\');">');
        document.write('<img src="{product_thumb_image}" {image_height} {image_width} border="0" title="{product_name}" alt="{product_name}" /></a>' );
        //]]></script>
        <noscript>
            <a href="{image_url}product/{product_full_image}" target="_blank" title="{product_name}">
            <img src="{product_thumb_image}" {image_height} {image_width} border="0" title="{product_name}" alt="{product_name}" />
            </a>
        </noscript>
        
    </div>
    <div>
        <h3><a style="font-size: 16px; font-weight: bold;" title="{product_name}" href="{product_flypage}">
            {product_name}</a>
        </h3>
        
        <div style="float:left;width:80%;">
            {product_s_desc}&nbsp;
            <a href="{product_flypage}" title="{product_details...}">{product_details...}...</a>
        </div>
        <br style="clear:both" />
        <div style="float:left;width:30%;">
            {product_price}
        </div>
        <div style="float:left;width:30%;text-align:center">
        {form_addtocart}
        </div>
        <div style="float:left;width:30%;">
        {product_rating}
        </div>
    </div>
</div>
