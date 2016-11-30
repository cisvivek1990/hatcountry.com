<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.1.1
 * @build     899
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Seo_Helper_Help extends Mirasvit_MstCore_Helper_Help
{
    protected $_help = array(
        'system' => array(
            //General Settings
            'general_is_add_canonical_url'              => 'If enabled, will add tag &lt;link rel="canonical" href="http://store.com/"&gt; to META-tags of your store.',
            'general_crossdomain'                       => 'Set default cross-domain canonical URL for multistore configuration.',
                                                                                                                                                                
            'general_canonical_url_ignore_pages'        => 'The list of pages where the Canonical Meta tag will not be added.<xmp></xmp>Can be a full action name or a request path. <xmp></xmp>Wildcards are allowed:
                                                        customer_account_*
                                                        /customer/account*
                                                        *customer/account*',

            'general_noindex_pages2'                    => 'Allows to add headers like "NOINDEX, FOLLOW", "INDEX, NOFOLLOW", "NOINDEX, NOFOLLOW" to any page of the store. <xmp></xmp>Can be a full action name or a request path. <xmp></xmp>Wildcards allowed. Examples:
                                                        customer_account_*
                                                        /customer/account*
                                                        *customer/account*
                                                        <xmp></xmp>Examples for layered navigation:
                                                        filterattribute_(manufacturer)
                                                        filterattribute_(1level)',
            
            'general_is_alternate_hreflang'             =>  'Sets "alternate" and "lang" tags for multilingual stores',
            'general_is_paging_prevnext'                => 'Adds to the head of your products list pages.',
            'general_is_rich_snippets'                  => 'Adds Rich snippets to product\'s pages. Snippets created using schema.org markup schema and microdata format.',
            'general_is_category_rich_snippets'         => 'Adds Rich snippets to category\'s pages. Snippets created using schema.org markup schema and microdata format. <b>Will create block in the bottom of product list page</b> to show average products rating and minimal price.',
            'general_breadcrumbs_separator'             => 'Allows to setup the separator for breandcrumb of rich snippets. This separator will be shown in the breandcrumb of Google search results. <xmp></xmp>Examples: <xmp>/&nbsp;, &nbsp;-&nbsp;, &rarr;</xmp> Leave field empty to disable rich snippets breadcrumbs.',
            'general_is_opengraph'                      => 'Adds Facebook Opengraph tags to the head of each product\'s page.',
            'general_robots_editor'                     => 'Allows to edit file robot.txt from browser. <xmp></xmp>File robots.txt should have 777 permissions.',
            'general_is_category_meta_tags_used'        => 'If set to \'NO\', meta tags of categories will be ignored and meta tags of category page will be generated only by template.',
            'general_is_product_meta_tags_used'         => 'If set to \'NO\', meta tags of products will be ignored and meta tags of product page will be generated only by template.',

            //SEO-friendly URLs Settings
            'url_layered_navigation_friendly_urls'      => 'If enabled, will make SEO friendly URLs in results of Layered Navigation filtering. <b>Will work only with native magento layered navigation.</b>',
            'url_trailing_slash'                        => 'Manage trailing slash “/” at the end of each store URL.',
            'url_product_url_format'                    => 'Allows to change URL format for your store. <xmp></xmp>You may select between short product URL (like http://store.com/product.html) and long product URL (like http://store.com/category1/category2/ product.html).',
            'url_product_url_key'                       => 'Allows to change a value of product keys by template. <b>To apply click "Apply Template For Product URLs"</b>
                                                            <xmp></xmp>You can use all products attributes as variables in format <b>[product_(attribute)]</b> <xmp></xmp>Example: [product_name] [product_sku] [by {product_manufacturer}] [color {product_color}]',
            
            'url_apply_template'                        => 'To ativate a new Product URL Key Template, click the button <b>Save config</b> to save SEO general settings. Only after this action press the button <b>Apply Template For Product URLs</b> to activate URL template.',
            'url_tag_friendly_urls'                     => 'If enabled, will make SEO friendly URLs for tags of products.',
            'url_review_friendly_urls'                  => 'If enabled, will make SEO friendly URLs for reviews of products.',
            
            //Product Images Settings
            'image_is_enable_image_friendly_urls'       => 'Will also create duplicate images in "/media/product" folder to be reachable via friendly URLs. <b>This feature can use a lot of HDD space.',
            'image_image_url_template'                  => 'Allows to automatically setup URLs of product images by template. <xmp></xmp>You can use variables in this template.<xmp></xmp>Example: [product_name] [product_sku] [by {product_manufacturer}] [color {product_color}]',
            'image_is_enable_image_alt'                 => 'If enabled, will generate alt and title for product images by template.',
            'image_image_alt_template'                  => 'Template to generate alt and title. <xmp></xmp>You can use variables in this template.<xmp></xmp>Example: [product_name], [product_sku], [by {product_manufacturer}], [color {product_color}].',
        ),
    );
}