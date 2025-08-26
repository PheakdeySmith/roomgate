<!DOCTYPE html>
<html data-wf-domain="flowis-b2b-saas-software-template.webflow.io" data-wf-page="68369d6ecd4dbc0b4f6e2f6e" data-wf-site="683588d6afb7bd5a9fb70ef5" data-wf-status="1" lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>RoomGate</title>
        <meta content="Flowis is a premium Webflow template designed for B2B SaaS platforms, sales teams, and growth-focused tech startups. Perfect for CRM platforms, sales enablement tools, and enterprise software solutions looking to establish a high-converting, modern, and professional online presence." name="description"/>
        <meta content="Flowis" property="og:title"/>
        <meta content="Flowis is a premium Webflow template designed for B2B SaaS platforms, sales teams, and growth-focused tech startups. Perfect for CRM platforms, sales enablement tools, and enterprise software solutions looking to establish a high-converting, modern, and professional online presence." property="og:description"/>
        <meta content="{{ asset('asset_frontend') }}/images/6839fbda9aceff2a6e6d1197_Open%20Graph%20Template.avif" property="og:image"/>
        <meta content="Flowis" property="twitter:title"/>
        <meta content="Flowis is a premium Webflow template designed for B2B SaaS platforms, sales teams, and growth-focused tech startups. Perfect for CRM platforms, sales enablement tools, and enterprise software solutions looking to establish a high-converting, modern, and professional online presence." property="twitter:description"/>
        <meta content="{{ asset('asset_frontend') }}/images/6839fbda9aceff2a6e6d1197_Open%20Graph%20Template.avif" property="twitter:image"/>
        <meta property="og:type" content="website"/>
        <meta content="summary_large_image" name="twitter:card"/>
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="Webflow" name="generator"/>
        <link href="{{ asset('asset_frontend') }}/css/flowis.min.css" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('asset_frontend') }}/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="https://fonts.googleapis.com" rel="preconnect"/>
        <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous"/>
        <script src="{{ asset('asset_frontend') }}/js/webfont.js" type="text/javascript"></script>
        <script type="text/javascript">
            WebFont.load({
                google: {
                    families: ["Geist:100,200,300,regular,500,600,700,800,900", "Geist Mono:regular"]
                }
            });
        </script>
        <script type="text/javascript">
            !function(o, c) {
                var n = c.documentElement
                  , t = " w-mod-";
                n.className += t + "js",
                ("ontouchstart"in o || o.DocumentTouch && c instanceof DocumentTouch) && (n.className += t + "touch")
            }(window, document);
        </script>
        <link rel="shortcut icon" href="{{ asset('assets') }}/images/favicon.ico">        
        <link href="{{ asset('asset_frontend') }}/images/6835899dc1c24f1d94bc3605_Webclip.png" rel="apple-touch-icon"/>
    </head>
    <body>
        <div class="page-wrapper">
            <div class="global-styles w-embed">
                <style>
                    /* Make text look crisper and more legible in all browsers */
                    body {
                        -webkit-font-smoothing: antialiased;
                        -moz-osx-font-smoothing: grayscale;
                        font-smoothing: antialiased;
                        text-rendering: optimizeLegibility;
                    }

                    /* Focus state style for keyboard navigation for the focusable elements */
                    *[tabindex]:focus-visible, input[type="file"]:focus-visible {
                        outline: 0.125rem solid #4d65ff;
                        outline-offset: 0.125rem;
                    }

                    /* Set color style to inherit */
                    .inherit-color * {
                        color: inherit;
                    }

                    /* Get rid of top margin on first element in any rich text element */
                    .w-richtext > :not(div):first-child, .w-richtext > div:first-child > :first-child {
                        margin-top: 0 !important;
                    }

                    /* Get rid of bottom margin on last element in any rich text element */
                    .w-richtext>:last-child, .w-richtext ol li:last-child, .w-richtext ul li:last-child {
                        margin-bottom: 0 !important;
                    }

                    /* Make sure containers never lose their center alignment */
                    .container-medium,.container-small, .container-large {
                        margin-right: auto !important;
                        margin-left: auto !important;
                    }

                    /* 
Make the following elements inherit typography styles from the parent and not have hardcoded values. 
Important: You will not be able to style for example "All Links" in Designer with this CSS applied.
Uncomment this CSS to use it in the project. Leave this message for future hand-off.
*/
                    /*
a,
.w-input,
.w-select,
.w-tab-link,
.w-nav-link,
.w-dropdown-btn,
.w-dropdown-toggle,
.w-dropdown-link {
  color: inherit;
  text-decoration: inherit;
  font-size: inherit;
}
*/
                    /* Apply "..." after 3 lines of text */
                    .text-style-3lines {
                        display: -webkit-box;
                        overflow: hidden;
                        -webkit-line-clamp: 3;
                        -webkit-box-orient: vertical;
                    }

                    /* Apply "..." after 2 lines of text */
                    .text-style-2lines {
                        display: -webkit-box;
                        overflow: hidden;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                    }

                    /* These classes are never overwritten */
                    .hide {
                        display: none !important;
                    }

                    @media screen and (max-width: 991px) {
                        .hide, .hide-tablet {
                            display: none !important;
                        }
                    }

                    @media screen and (max-width: 767px) {
                        .hide-mobile-landscape {
                            display: none !important;
                        }

                        .nav-menu {
                            height: calc(100vh - 72px) !important;
                        }
                    }

                    @media screen and (max-width: 479px) {
                        .hide-mobile {
                            display: none !important;
                        }
                    }

                    .margin-0 {
                        margin: 0rem !important;
                    }

                    .padding-0 {
                        padding: 0rem !important;
                    }

                    .spacing-clean {
                        padding: 0rem !important;
                        margin: 0rem !important;
                    }

                    .margin-top {
                        margin-right: 0rem !important;
                        margin-bottom: 0rem !important;
                        margin-left: 0rem !important;
                    }

                    .padding-top {
                        padding-right: 0rem !important;
                        padding-bottom: 0rem !important;
                        padding-left: 0rem !important;
                    }

                    .margin-right {
                        margin-top: 0rem !important;
                        margin-bottom: 0rem !important;
                        margin-left: 0rem !important;
                    }

                    .padding-right {
                        padding-top: 0rem !important;
                        padding-bottom: 0rem !important;
                        padding-left: 0rem !important;
                    }

                    .margin-bottom {
                        margin-top: 0rem !important;
                        margin-right: 0rem !important;
                        margin-left: 0rem !important;
                    }

                    .padding-bottom {
                        padding-top: 0rem !important;
                        padding-right: 0rem !important;
                        padding-left: 0rem !important;
                    }

                    .margin-left {
                        margin-top: 0rem !important;
                        margin-right: 0rem !important;
                        margin-bottom: 0rem !important;
                    }

                    .padding-left {
                        padding-top: 0rem !important;
                        padding-right: 0rem !important;
                        padding-bottom: 0rem !important;
                    }

                    .margin-horizontal {
                        margin-top: 0rem !important;
                        margin-bottom: 0rem !important;
                    }

                    .padding-horizontal {
                        padding-top: 0rem !important;
                        padding-bottom: 0rem !important;
                    }

                    .margin-vertical {
                        margin-right: 0rem !important;
                        margin-left: 0rem !important;
                    }

                    .padding-vertical {
                        padding-right: 0rem !important;
                        padding-left: 0rem !important;
                    }
                </style>
            </div>
            <div data-animation="over-left" class="navbar w-nav" data-easing2="ease" data-easing="ease" data-collapse="medium" role="banner" data-no-scroll="1" data-duration="400" data-doc-height="1">
                <header class="nav-content">
                    <div class="nav-background"></div>
                    <div class="container w-container">
                        <div class="nav-wrapper">
                            <a id="w-node-_2570e665-cf5d-8a22-a751-69d80fe8b425-0fe8b420" href="/" class="nav-brand w-nav-brand">
                                <img loading="lazy" src="{{ asset('asset_frontend') }}/images/logo-dark.png" alt="Flowis Logo" class="nav-logo"/>
                            </a>
                            <div class="nav-menu-wrapper">
                                <nav role="navigation" class="nav-menu w-nav-menu">
                                    <a href="/" class="nav-link w-nav-link">Overview</a>
                                    <div data-delay="200" data-hover="false" class="nav-dropdown w-dropdown">
                                        <div class="nav-dropdown-toggle w-dropdown-toggle">
                                            <div class="dot"></div>
                                            <div>Pages</div>
                                        </div>
                                        <nav class="nav-dropdown-wrapper w-dropdown-list">
                                            <div class="nav-dropdown-grid">
                                                <div class="nav-dropdown-column">
                                                    <div class="text-style-badge">MULTILAYOUT</div>
                                                    <div class="nav-dropdown-multilayout">
                                                        <div class="nav-dropdown-multilayout-item">
                                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/68359b71a159c5ecfa2a44d0_home%20icon.avif" alt="" class="icon-height-small"/>
                                                            <div class="nav-dropdown-multilayout-content">
                                                                <div class="text-size-small text-weight-medium">Home</div>
                                                                <div class="nav-dropdown-multilayout-links">
                                                                    <a href="/home-v1" aria-current="page" class="nav-dropdown-multilayout-link w--current">Version 1</a>
                                                                    <div class="dot"></div>
                                                                    <a href="/home-v2" class="nav-dropdown-multilayout-link">Version 2</a>
                                                                    <div class="dot"></div>
                                                                    <a href="/home-v3" class="nav-dropdown-multilayout-link">Version 3</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="nav-dropdown-multilayout-item">
                                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/68359b7190ec2ca4c7e46861_features%20icon.avif" alt="" class="icon-height-small"/>
                                                            <div class="nav-dropdown-multilayout-content">
                                                                <div class="text-size-small text-weight-medium">Features</div>
                                                                <div class="nav-dropdown-multilayout-links">
                                                                    <a href="/features-v1" class="nav-dropdown-multilayout-link">Version 1</a>
                                                                    <div class="dot"></div>
                                                                    <a href="/features-v2" class="nav-dropdown-multilayout-link">Version 2</a>
                                                                    <div class="dot"></div>
                                                                    <a href="/features-v3" class="nav-dropdown-multilayout-link">Version 3</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="nav-dropdown-multilayout-item">
                                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/68359b72893be89905f654ec_contact%20icon.avif" alt="" class="icon-height-small"/>
                                                            <div class="nav-dropdown-multilayout-content">
                                                                <div class="text-size-small text-weight-medium">Contact</div>
                                                                <div class="nav-dropdown-multilayout-links">
                                                                    <a href="/contact-v1" class="nav-dropdown-multilayout-link">Version 1</a>
                                                                    <div class="dot"></div>
                                                                    <a href="/contact-v2" class="nav-dropdown-multilayout-link">Version 2</a>
                                                                    <div class="dot"></div>
                                                                    <a href="/contact-v3" class="nav-dropdown-multilayout-link">Version 3</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nav-dropdown-column">
                                                    <div class="text-style-badge">Company</div>
                                                    <div class="nav-dropdown-links">
                                                        <a href="/industries" class="nav-dropdown-link w-dropdown-link">Industries</a>
                                                        <a href="/industry/technology" class="nav-dropdown-link w-dropdown-link">Industry Solution</a>
                                                        <a href="/integrations" class="nav-dropdown-link w-dropdown-link">Integrations</a>
                                                        <a href="/integration/blipify" class="nav-dropdown-link w-dropdown-link">Integration Details</a>
                                                        <a href="/about" class="nav-dropdown-link w-dropdown-link">About</a>
                                                        <a href="/category/plans" class="nav-dropdown-link w-dropdown-link">Pricing</a>
                                                        <a href="/product/enterprise" class="nav-dropdown-link w-dropdown-link">Plan</a>
                                                        <a href="/blog" class="nav-dropdown-link w-dropdown-link">Blog</a>
                                                        <a href="/blog/how-to-migrate-to-flowis-in-under-a-day" class="nav-dropdown-link w-dropdown-link">Blog Post</a>
                                                        <a href="/api" class="nav-dropdown-link w-dropdown-link">API</a>
                                                    </div>
                                                </div>
                                                <div class="nav-dropdown-column">
                                                    <div class="text-style-badge">Account &amp;Utility</div>
                                                    <div class="nav-dropdown-links">
                                                        <a href="/utility/terms" class="nav-dropdown-link w-dropdown-link">Terms &amp;Conditions</a>
                                                        <a href="/account/sign-in" class="nav-dropdown-link w-dropdown-link">Sign in</a>
                                                        <a href="/account/sign-up" class="nav-dropdown-link w-dropdown-link">Sign up</a>
                                                        <a href="/account/forgot-password" class="nav-dropdown-link w-dropdown-link">Forgot Password</a>
                                                        <a href="/utility/demo" class="nav-dropdown-link w-dropdown-link">Demo</a>
                                                        <a href="/404" class="nav-dropdown-link w-dropdown-link">404</a>
                                                        <a href="/401" class="nav-dropdown-link w-dropdown-link">Password Protected</a>
                                                    </div>
                                                </div>
                                                <div class="nav-dropdown-column">
                                                    <div class="text-style-badge">Template</div>
                                                    <div class="nav-dropdown-links">
                                                        <a href="/template/style-guide" class="nav-dropdown-link w-dropdown-link">Style Guide</a>
                                                        <a href="/template/licenses" class="nav-dropdown-link w-dropdown-link">Licenses</a>
                                                        <a href="/template/changelog" class="nav-dropdown-link w-dropdown-link">Changelog</a>
                                                    </div>
                                                </div>
                                                <a href="https://webflow.com/templates/html/flowis-website-template" target="_blank" class="nav-dropdown-column is-buy-template w-inline-block">
                                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/68359c2fb383a12e62ab862d_template%20preview.avif" alt="B2B SaaS platforms webflow template"/>
                                                    <div class="nav-dropdown-buy-content">
                                                        <div class="nav-dropdown-buy-heading">
                                                            <div>Buy Flowis</div>
                                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/680b71a3a9815f2cd1c0d011_Right%20Up.svg" alt="" class="icon-height-small-4"/>
                                                        </div>
                                                        <div class="text-size-tiny text-style-muted">The all-in-one platform for managing your rental properties, tenants, and finances with ease.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </nav>
                                    </div>
                                    <div class="nav-links-buttons">
                                        <a href="http://loonis.co/" target="_blank" class="button is-secondary w-button">More Templates</a>
                                        <a href="https://webflow.com/templates/html/flowis-website-template" target="_blank" class="button w-button">Buy Flowis</a>
                                    </div>
                                </nav>
                                {{-- <div data-open-product="" data-wf-cart-type="modal" data-wf-cart-query="query Dynamo2 {
  database {
    id
    commerceOrder {
      comment
      extraItems {
        name
        pluginId
        pluginName
        price {
          value
          unit
          decimalValue
          string
        }
      }
      id
      startedOn
      statusFlags {
        hasDownloads
        hasSubscription
        isFreeOrder
        requiresShipping
      }
      subtotal {
        value
        unit
        decimalValue
        string
      }
      total {
        value
        unit
        decimalValue
        string
      }
      updatedOn
      userItems {
        count
        id
        product {
          id
          cmsLocaleId
          f__draft_0ht
          f__archived_0ht
          f_name_
          f_sku_properties_3dr {
            id
            name
            enum {
              id
              name
              slug
            }
          }
        }
        rowTotal {
          value
          unit
          decimalValue
          string
        }
        sku {
          cmsLocaleId
          f__draft_0ht
          f__archived_0ht
          f_main_image_4dr {
            url
            file {
              size
              origFileName
              createdOn
              updatedOn
              mimeType
              width
              height
              variants {
                origFileName
                quality
                height
                width
                s3Url
                error
                size
              }
            }
            alt
          }
          f_sku_values_3dr {
            property {
              id
            }
            value {
              id
            }
          }
          id
        }
        subscriptionFrequency
        subscriptionInterval
        subscriptionTrial
      }
      userItemsCount
    }
  }
  site {
    id
    commerce {
      businessAddress {
        country
      }
      defaultCountry
      defaultCurrency
      quickCheckoutEnabled
    }
  }
}" data-wf-page-link-href-prefix="" class="w-commerce-commercecartwrapper" data-node-type="commerce-cart-wrapper">
                                    <a class="w-commerce-commercecartopenlink nav-link is-cart w-inline-block" role="button" aria-haspopup="dialog" aria-label="Open cart" data-node-type="commerce-cart-open-link" href="#">
                                        <div class="w-inline-block">Cart </div>
                                        <div class="cart-quantity-wapper">
                                            <div>(</div>
                                            <div data-wf-bindings="%5B%7B%22innerHTML%22%3A%7B%22type%22%3A%22Number%22%2C%22filter%22%3A%7B%22type%22%3A%22numberPrecision%22%2C%22params%22%3A%5B%220%22%2C%22numberPrecision%22%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItemsCount%22%7D%7D%5D" class="w-commerce-commercecartopenlinkcount cart-quantity">0</div>
                                            <div>)</div>
                                        </div>
                                    </a>
                                    <div style="display:none" class="w-commerce-commercecartcontainerwrapper w-commerce-commercecartcontainerwrapper--cartType-modal cart-wrapper" data-node-type="commerce-cart-container-wrapper">
                                        <div data-node-type="commerce-cart-container" role="dialog" class="w-commerce-commercecartcontainer cart-container">
                                            <div class="w-commerce-commercecartheader cart-header">
                                                <h4 class="w-commerce-commercecartheading cart-heading">Your Cart</h4>
                                                <a class="w-commerce-commercecartcloselink cart-close w-inline-block" role="button" aria-label="Close cart" data-node-type="commerce-cart-close-link">
                                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/680b71a3a9815f2cd1c0d012_close%20icon.svg" alt="" class="cart-close-icon"/>
                                                </a>
                                            </div>
                                            <div class="w-commerce-commercecartformwrapper">
                                                <form style="display:none" class="w-commerce-commercecartform" data-node-type="commerce-cart-form">
                                                    <script type="text/x-wf-template" id="wf-template-2570e665-cf5d-8a22-a751-69d80fe8b4a6">
                                                        %3Cdiv%20class%3D%22w-commerce-commercecartitem%20cart-item%22%3E%3Cimg%20src%3D%22https%3A%2F%2Fcdn.prod.website-files.com%2Fplugins%2FBasic%2Fassets%2Fplaceholder.60f9b1840c.svg%22%20data-wf-bindings%3D%22%255B%257B%2522src%2522%253A%257B%2522type%2522%253A%2522ImageRef%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.sku.f_main_image_4dr%2522%257D%257D%255D%22%20alt%3D%22%22%20class%3D%22w-commerce-commercecartitemimage%20w-dyn-bind-empty%22%2F%3E%3Cdiv%20class%3D%22w-commerce-commercecartiteminfo%20cart-text%22%3E%3Cdiv%20data-wf-bindings%3D%22%255B%257B%2522innerHTML%2522%253A%257B%2522type%2522%253A%2522PlainText%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.product.f_name_%2522%257D%257D%255D%22%20class%3D%22w-commerce-commercecartproductname%20cart-title%20w-dyn-bind-empty%22%3E%3C%2Fdiv%3E%3Cdiv%20class%3D%22cart-price%22%3EThis%20is%20some%20text%20inside%20of%20a%20div%20block.%3C%2Fdiv%3E%3Cscript%20type%3D%22text%2Fx-wf-template%22%20id%3D%22wf-template-2570e665-cf5d-8a22-a751-69d80fe8b4ad%22%3E%253Cli%253E%253Cspan%2520data-wf-bindings%253D%2522%25255B%25257B%252522innerHTML%252522%25253A%25257B%252522type%252522%25253A%252522PlainText%252522%25252C%252522filter%252522%25253A%25257B%252522type%252522%25253A%252522identity%252522%25252C%252522params%252522%25253A%25255B%25255D%25257D%25252C%252522dataPath%252522%25253A%252522database.commerceOrder.userItems%25255B%25255D.product.f_sku_properties_3dr%25255B%25255D.name%252522%25257D%25257D%25255D%2522%253E%253C%252Fspan%253E%253Cspan%253E%253A%2520%253C%252Fspan%253E%253Cspan%2520data-wf-bindings%253D%2522%25255B%25257B%252522innerHTML%252522%25253A%25257B%252522type%252522%25253A%252522CommercePropValues%252522%25252C%252522filter%252522%25253A%25257B%252522type%252522%25253A%252522identity%252522%25252C%252522params%252522%25253A%25255B%25255D%25257D%25252C%252522dataPath%252522%25253A%252522database.commerceOrder.userItems%25255B%25255D.product.f_sku_properties_3dr%25255B%25255D%252522%25257D%25257D%25255D%2522%253E%253C%252Fspan%253E%253C%252Fli%253E%3C%2Fscript%3E%3Cul%20data-wf-bindings%3D%22%255B%257B%2522optionSets%2522%253A%257B%2522type%2522%253A%2522CommercePropTable%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.product.f_sku_properties_3dr%5B%5D%2522%257D%257D%252C%257B%2522optionValues%2522%253A%257B%2522type%2522%253A%2522CommercePropValues%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.sku.f_sku_values_3dr%2522%257D%257D%255D%22%20class%3D%22w-commerce-commercecartoptionlist%22%20data-wf-collection%3D%22database.commerceOrder.userItems%255B%255D.product.f_sku_properties_3dr%22%20data-wf-template-id%3D%22wf-template-2570e665-cf5d-8a22-a751-69d80fe8b4ad%22%3E%3Cli%3E%3Cspan%20data-wf-bindings%3D%22%255B%257B%2522innerHTML%2522%253A%257B%2522type%2522%253A%2522PlainText%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.product.f_sku_properties_3dr%255B%255D.name%2522%257D%257D%255D%22%3E%3C%2Fspan%3E%3Cspan%3E%3A%20%3C%2Fspan%3E%3Cspan%20data-wf-bindings%3D%22%255B%257B%2522innerHTML%2522%253A%257B%2522type%2522%253A%2522CommercePropValues%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.product.f_sku_properties_3dr%255B%255D%2522%257D%257D%255D%22%3E%3C%2Fspan%3E%3C%2Fli%3E%3C%2Ful%3E%3Ca%20href%3D%22%23%22%20role%3D%22button%22%20data-wf-bindings%3D%22%255B%257B%2522data-commerce-sku-id%2522%253A%257B%2522type%2522%253A%2522ItemRef%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.sku.id%2522%257D%257D%255D%22%20class%3D%22cart-remove-button%20w-inline-block%22%20data-wf-cart-action%3D%22remove-item%22%20data-commerce-sku-id%3D%22%22%20aria-label%3D%22Remove%20item%20from%20cart%22%3E%3Cdiv%3ERemove%3C%2Fdiv%3E%3C%2Fa%3E%3C%2Fdiv%3E%3Cinput%20aria-label%3D%22Update%20quantity%22%20data-wf-bindings%3D%22%255B%257B%2522value%2522%253A%257B%2522type%2522%253A%2522Number%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522numberPrecision%2522%252C%2522params%2522%253A%255B%25220%2522%252C%2522numberPrecision%2522%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.count%2522%257D%257D%252C%257B%2522data-commerce-sku-id%2522%253A%257B%2522type%2522%253A%2522ItemRef%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.sku.id%2522%257D%257D%255D%22%20class%3D%22w-commerce-commercecartquantity%22%20required%3D%22%22%20pattern%3D%22%5E%5B0-9%5D%2B%24%22%20inputMode%3D%22numeric%22%20type%3D%22number%22%20name%3D%22quantity%22%20autoComplete%3D%22off%22%20data-wf-cart-action%3D%22update-item-quantity%22%20data-commerce-sku-id%3D%22%22%20value%3D%221%22%2F%3E%3C%2Fdiv%3E
                                                    </script>
                                                    <div class="w-commerce-commercecartlist" data-wf-collection="database.commerceOrder.userItems" data-wf-template-id="wf-template-2570e665-cf5d-8a22-a751-69d80fe8b4a6">
                                                        <div class="w-commerce-commercecartitem cart-item">
                                                            <img src="https://cdn.prod.website-files.com/plugins/Basic/assets/placeholder.60f9b1840c.svg" data-wf-bindings="%5B%7B%22src%22%3A%7B%22type%22%3A%22ImageRef%22%2C%22filter%22%3A%7B%22type%22%3A%22identity%22%2C%22params%22%3A%5B%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItems%5B%5D.sku.f_main_image_4dr%22%7D%7D%5D" alt="" class="w-commerce-commercecartitemimage w-dyn-bind-empty"/>
                                                            <div class="w-commerce-commercecartiteminfo cart-text">
                                                                <div data-wf-bindings="%5B%7B%22innerHTML%22%3A%7B%22type%22%3A%22PlainText%22%2C%22filter%22%3A%7B%22type%22%3A%22identity%22%2C%22params%22%3A%5B%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItems%5B%5D.product.f_name_%22%7D%7D%5D" class="w-commerce-commercecartproductname cart-title w-dyn-bind-empty"></div>
                                                                <div class="cart-price">This is some text inside of a div block.</div>
                                                                <script type="text/x-wf-template" id="wf-template-2570e665-cf5d-8a22-a751-69d80fe8b4ad">
                                                                    %3Cli%3E%3Cspan%20data-wf-bindings%3D%22%255B%257B%2522innerHTML%2522%253A%257B%2522type%2522%253A%2522PlainText%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.product.f_sku_properties_3dr%255B%255D.name%2522%257D%257D%255D%22%3E%3C%2Fspan%3E%3Cspan%3E%3A%20%3C%2Fspan%3E%3Cspan%20data-wf-bindings%3D%22%255B%257B%2522innerHTML%2522%253A%257B%2522type%2522%253A%2522CommercePropValues%2522%252C%2522filter%2522%253A%257B%2522type%2522%253A%2522identity%2522%252C%2522params%2522%253A%255B%255D%257D%252C%2522dataPath%2522%253A%2522database.commerceOrder.userItems%255B%255D.product.f_sku_properties_3dr%255B%255D%2522%257D%257D%255D%22%3E%3C%2Fspan%3E%3C%2Fli%3E
                                                                </script>
                                                                <ul data-wf-bindings="%5B%7B%22optionSets%22%3A%7B%22type%22%3A%22CommercePropTable%22%2C%22filter%22%3A%7B%22type%22%3A%22identity%22%2C%22params%22%3A%5B%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItems%5B%5D.product.f_sku_properties_3dr[]%22%7D%7D%2C%7B%22optionValues%22%3A%7B%22type%22%3A%22CommercePropValues%22%2C%22filter%22%3A%7B%22type%22%3A%22identity%22%2C%22params%22%3A%5B%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItems%5B%5D.sku.f_sku_values_3dr%22%7D%7D%5D" class="w-commerce-commercecartoptionlist" data-wf-collection="database.commerceOrder.userItems%5B%5D.product.f_sku_properties_3dr" data-wf-template-id="wf-template-2570e665-cf5d-8a22-a751-69d80fe8b4ad">
                                                                    <li>
                                                                        <span data-wf-bindings="%5B%7B%22innerHTML%22%3A%7B%22type%22%3A%22PlainText%22%2C%22filter%22%3A%7B%22type%22%3A%22identity%22%2C%22params%22%3A%5B%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItems%5B%5D.product.f_sku_properties_3dr%5B%5D.name%22%7D%7D%5D"></span>
                                                                        <span>: </span>
                                                                        <span data-wf-bindings="%5B%7B%22innerHTML%22%3A%7B%22type%22%3A%22CommercePropValues%22%2C%22filter%22%3A%7B%22type%22%3A%22identity%22%2C%22params%22%3A%5B%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItems%5B%5D.product.f_sku_properties_3dr%5B%5D%22%7D%7D%5D"></span>
                                                                    </li>
                                                                </ul>
                                                                <a href="#" role="button" data-wf-bindings="%5B%7B%22data-commerce-sku-id%22%3A%7B%22type%22%3A%22ItemRef%22%2C%22filter%22%3A%7B%22type%22%3A%22identity%22%2C%22params%22%3A%5B%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItems%5B%5D.sku.id%22%7D%7D%5D" class="cart-remove-button w-inline-block" data-wf-cart-action="remove-item" data-commerce-sku-id="" aria-label="Remove item from cart">
                                                                    <div>Remove</div>
                                                                </a>
                                                            </div>
                                                            <input aria-label="Update quantity" data-wf-bindings="%5B%7B%22value%22%3A%7B%22type%22%3A%22Number%22%2C%22filter%22%3A%7B%22type%22%3A%22numberPrecision%22%2C%22params%22%3A%5B%220%22%2C%22numberPrecision%22%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItems%5B%5D.count%22%7D%7D%2C%7B%22data-commerce-sku-id%22%3A%7B%22type%22%3A%22ItemRef%22%2C%22filter%22%3A%7B%22type%22%3A%22identity%22%2C%22params%22%3A%5B%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.userItems%5B%5D.sku.id%22%7D%7D%5D" class="w-commerce-commercecartquantity" required="" pattern="^[0-9]+$" inputMode="numeric" type="number" name="quantity" autoComplete="off" data-wf-cart-action="update-item-quantity" data-commerce-sku-id="" value="1"/>
                                                        </div>
                                                    </div>
                                                    <div class="w-commerce-commercecartfooter cart-footer">
                                                        <div aria-live="polite" aria-atomic="true" class="w-commerce-commercecartlineitem">
                                                            <div>Subtotal</div>
                                                            <div data-wf-bindings="%5B%7B%22innerHTML%22%3A%7B%22type%22%3A%22CommercePrice%22%2C%22filter%22%3A%7B%22type%22%3A%22price%22%2C%22params%22%3A%5B%5D%7D%2C%22dataPath%22%3A%22database.commerceOrder.subtotal%22%7D%7D%5D" class="w-commerce-commercecartordervalue cart-price is-final"></div>
                                                        </div>
                                                        <div>
                                                            <div data-node-type="commerce-cart-quick-checkout-actions" style="display:none">
                                                                <a role="button" aria-haspopup="dialog" aria-label="Apple Pay" data-node-type="commerce-cart-apple-pay-button" style="background-image:-webkit-named-image(apple-pay-logo-white);background-size:100% 50%;background-position:50% 50%;background-repeat:no-repeat" class="w-commerce-commercecartapplepaybutton" tabindex="0">
                                                                    <div></div>
                                                                </a>
                                                                <a role="button" tabindex="0" aria-haspopup="dialog" data-node-type="commerce-cart-quick-checkout-button" style="display:none" class="w-commerce-commercecartquickcheckoutbutton">
                                                                    <svg class="w-commerce-commercequickcheckoutgoogleicon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 16 16">
                                                                        <defs>
                                                                            <polygon id="google-mark-a" points="0 .329 3.494 .329 3.494 7.649 0 7.649"></polygon>
                                                                            <polygon id="google-mark-c" points=".894 0 13.169 0 13.169 6.443 .894 6.443"></polygon>
                                                                        </defs>
                                                                        <g fill="none" fill-rule="evenodd">
                                                                            <path fill="#4285F4" d="M10.5967,12.0469 L10.5967,14.0649 L13.1167,14.0649 C14.6047,12.6759 15.4577,10.6209 15.4577,8.1779 C15.4577,7.6339 15.4137,7.0889 15.3257,6.5559 L7.8887,6.5559 L7.8887,9.6329 L12.1507,9.6329 C11.9767,10.6119 11.4147,11.4899 10.5967,12.0469"></path>
                                                                            <path fill="#34A853" d="M7.8887,16 C10.0137,16 11.8107,15.289 13.1147,14.067 C13.1147,14.066 13.1157,14.065 13.1167,14.064 L10.5967,12.047 C10.5877,12.053 10.5807,12.061 10.5727,12.067 C9.8607,12.556 8.9507,12.833 7.8887,12.833 C5.8577,12.833 4.1387,11.457 3.4937,9.605 L0.8747,9.605 L0.8747,11.648 C2.2197,14.319 4.9287,16 7.8887,16"></path>
                                                                            <g transform="translate(0 4)">
                                                                                <mask id="google-mark-b" fill="#fff">
                                                                                    <use xlink:href="#google-mark-a"></use>
                                                                                </mask>
                                                                                <path fill="#FBBC04" d="M3.4639,5.5337 C3.1369,4.5477 3.1359,3.4727 3.4609,2.4757 L3.4639,2.4777 C3.4679,2.4657 3.4749,2.4547 3.4789,2.4427 L3.4939,0.3287 L0.8939,0.3287 C0.8799,0.3577 0.8599,0.3827 0.8459,0.4117 C-0.2821,2.6667 -0.2821,5.3337 0.8459,7.5887 L0.8459,7.5997 C0.8549,7.6167 0.8659,7.6317 0.8749,7.6487 L3.4939,5.6057 C3.4849,5.5807 3.4729,5.5587 3.4639,5.5337" mask="url(#google-mark-b)"></path>
                                                                            </g>
                                                                            <mask id="google-mark-d" fill="#fff">
                                                                                <use xlink:href="#google-mark-c"></use>
                                                                            </mask>
                                                                            <path fill="#EA4335" d="M0.894,4.3291 L3.478,6.4431 C4.113,4.5611 5.843,3.1671 7.889,3.1671 C9.018,3.1451 10.102,3.5781 10.912,4.3671 L13.169,2.0781 C11.733,0.7231 9.85,-0.0219 7.889,0.0001 C4.941,0.0001 2.245,1.6791 0.894,4.3291" mask="url(#google-mark-d)"></path>
                                                                        </g>
                                                                    </svg>
                                                                    <svg class="w-commerce-commercequickcheckoutmicrosofticon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                                        <g fill="none" fill-rule="evenodd">
                                                                            <polygon fill="#F05022" points="7 7 1 7 1 1 7 1"></polygon>
                                                                            <polygon fill="#7DB902" points="15 7 9 7 9 1 15 1"></polygon>
                                                                            <polygon fill="#00A4EE" points="7 15 1 15 1 9 7 9"></polygon>
                                                                            <polygon fill="#FFB700" points="15 15 9 15 9 9 15 9"></polygon>
                                                                        </g>
                                                                    </svg>
                                                                    <div>Pay with browser.</div>
                                                                </a>
                                                            </div>
                                                            <a href="/checkout" value="Continue to Checkout" class="w-commerce-commercecartcheckoutbutton button" data-loading-text="Hang Tight..." data-node-type="cart-checkout-button">Continue to Checkout</a>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="w-commerce-commercecartemptystate">
                                                    <div aria-label="This cart is empty" aria-live="polite">No items found.</div>
                                                </div>
                                                <div aria-live="assertive" style="display:none" data-node-type="commerce-cart-error" class="w-commerce-commercecarterrorstate">
                                                    <div class="w-cart-error-msg" data-w-cart-quantity-error="Product is not available in this quantity." data-w-cart-general-error="Something went wrong when adding this item to the cart." data-w-cart-checkout-error="Checkout is disabled on this site." data-w-cart-cart_order_min-error="The order minimum was not met. Add more items to your cart to continue." data-w-cart-subscription_error-error="Before you purchase, please use your email invite to verify your address so we can send order updates.">Product is not available in this quantity.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            <div id="w-node-_2570e665-cf5d-8a22-a751-69d80fe8b4cd-0fe8b420" class="nav-buttons">
                                <div data-delay="0" data-hover="false" class="nav-info-dropdown w-dropdown">
                                    <div class="nav-info-dropdown-toggle w-dropdown-toggle">
                                        <div class="button is-icon is-secondary hide-tablet">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/680b71a3a9815f2cd1c0d005_info.svg" alt="" class="icon-height-small"/>
                                        </div>
                                    </div>
                                    <nav class="nav-info-dropdown-wrapper w-dropdown-list">
                                        <div class="nav-dropdown-column">
                                            <div class="text-style-badge">Key Features</div>
                                            <div class="nav-info-dropdown-list">
                                                <div class="nav-info-dropdown-list-item">
                                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/680b71a3a9815f2cd1c0d00b_Checkmark2.svg" alt="" class="icon-height-small"/>
                                                    <div class="text-size-small text-weight-medium">Figma file available</div>
                                                </div>
                                                <div class="nav-info-dropdown-list-item">
                                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/680b71a3a9815f2cd1c0d00b_Checkmark2.svg" alt="" class="icon-height-small"/>
                                                    <div class="text-size-small text-weight-medium">Built with “Client First”</div>
                                                </div>
                                                <div class="nav-info-dropdown-list-item">
                                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/680b71a3a9815f2cd1c0d00b_Checkmark2.svg" alt="" class="icon-height-small"/>
                                                    <div class="text-size-small text-weight-medium">20+ pages</div>
                                                </div>
                                                <div class="nav-info-dropdown-list-item">
                                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/680b71a3a9815f2cd1c0d00b_Checkmark2.svg" alt="" class="icon-height-small-4"/>
                                                    <div class="text-size-small text-weight-medium">60+ unique sections</div>
                                                </div>
                                                <div class="nav-info-dropdown-list-item">
                                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/680b71a3a9815f2cd1c0d00b_Checkmark2.svg" alt="" class="icon-height-small"/>
                                                    <div class="text-size-small text-weight-medium">E-commerce and CMS powered</div>
                                                </div>
                                            </div>
                                        </div>
                                    </nav>
                                </div>
                                <a href="{{ route('login')}}" class="button hide-tablet w-button">Login</a>
                                <div class="nav-menu-button w-nav-button">
                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/680b71a3a9815f2cd1c0d00a_Hamburger%20menu.svg" alt="" class="nav-menu-icon"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            </div>
            <main class="main-wrapper">

                <section class="section_home1_hero">
                    <div class="padding-global">
                        <div data-w-id="ca66c9a1-6754-411c-5d58-64c2b94ef88e" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="home1_hero_grid">
                            <div class="home1_hero_content">
                                <div>
                                    <div class="text-style-badge text-color-white">/ Welcome to RoomGate</div>
                                    <div class="spacer-small"></div>
                                    <h1>Room Rental System That Powers Real Growth</h1>
                                    <div class="spacer-xsmall"></div>
                                    <div class="max-width-medium text-style-muted80">Connecting landlords and tenants for a seamless and transparent rental experience.</div>
                                    <div class="spacer-small"></div>
                                    <div class="button-group">
                                        <a href="/contact-v1" class="button is-secondary w-button">Start Free Trial</a>
                                        <a href="/features-v1" class="button is-outline w-button">Explore Features</a>
                                    </div>
                                    <div class="spacer-small"></div>
                                    <div class="home1_hero_reviews">
                                        <div class="home1_hero_reviews-authors">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/avatar1.avif" alt="RoomGate avatar1" class="home1_hero_image-2 is-first"/>
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/avatar2.avif" alt="RoomGate avatar2" class="home1_hero_image-2"/>
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/avatar4.avif" alt="RoomGate avatar4" class="home1_hero_image-2"/>
                                        </div>
                                        <img loading="lazy" src="{{ asset('asset_frontend') }}/images/reviews_star.png" alt="5 Stars" class="home1_hero_reviews-stars"/>
                                        <div class="text-size-tiny text-weight-medium">4.8/5</div>
                                        <div class="text-size-tiny text-weight-medium">610+ Reviews</div>
                                    </div>
                                    <div class="spacer-medium"></div>
                                </div>
                                <a href="#" class="home1_hero_lightbox w-inline-block w-lightbox">
                                    <div data-autoplay="true" data-loop="true" data-wf-ignore="true" class="home1_hero_video w-background-video w-background-video-atom">
                                        <video id="a74ced63-dc72-71c7-dc2b-a53869f694c7-video" autoplay="" loop=""  muted="" playsinline="" data-wf-ignore="true" data-object-fit="cover">
                                            <source src="{{ asset('asset_frontend') }}/images/video.mp4" data-wf-ignore="true"/>
                                        </video>
                                    </div>
                                    <div class="play">
                                        <img loading="lazy" src="{{ asset('asset_frontend') }}/images/Play.svg" alt="" class="icon-height-small"/>
                                        <div class="text-color-alternate">See in action</div>
                                    </div>
                                    <script type="application/json" class="w-json">
                                        {
                                            "items": [
                                                {
                                                    "url": "https://www.youtube.com/watch?v=fSQaE6Dcr-s&ab_channel=MBTRMUSIK",
                                                    "originalUrl": "https://www.youtube.com/watch?v=fSQaE6Dcr-s&ab_channel=MBTRMUSIK",
                                                    "width": 940,
                                                    "height": 528,
                                                    "thumbnailUrl": "https://i.ytimg.com/vi/fSQaE6Dcr-s/hqdefault.jpg",
                                                    "html": "<iframe class=\"embedly-embed\" src=\"//cdn.embedly.com/widgets/media.html?src=https%3A%2F%2Fwww.youtube.com%2Fembed%2FfSQaE6Dcr-s%3Ffeature%3Doembed&display_name=YouTube&url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DfSQaE6Dcr-s&image=https%3A%2F%2Fi.ytimg.com%2Fvi%2FfSQaE6Dcr-s%2Fhqdefault.jpg&type=text%2Fhtml&schema=youtube\" width=\"940\" height=\"528\" scrolling=\"no\" title=\"YouTube embed\" frameborder=\"0\" allow=\"autoplay; fullscreen; encrypted-media; picture-in-picture;\" allowfullscreen=\"true\"></iframe>",
                                                    "type": "video"
                                                }
                                            ],
                                            "group": ""
                                        }</script>
                                </a>
                            </div>
                            <div class="home1_hero_image-box">
                                <img src="{{ asset('asset_frontend') }}/images/dashbaord.png" loading="lazy" 
                                sizes="(max-width: 1446px) 100vw, 1446px" srcset="
                                {{ asset('asset_frontend') }}/images/dashboard-p-500.avif 500w, 
                                {{ asset('asset_frontend') }}/images/dashboard-p-800.avif 800w, 
                                {{ asset('asset_frontend') }}/images/dashboard.avif 1446w" alt="RoomGate dashboard" class="home1_hero_image"/>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_logo">
                    <div class="padding-global padding-section-small">
                        <div class="container-default">
                            <div class="text-size-large text-align-center">Trusted by Fast-Moving B2B Teams</div>
                            <div class="spacer-small"></div>
                            <div data-w-id="59d93b94-d4b4-ce8d-227a-4160ce6bb8d5" class="logos_marquee">
                                <div class="logos_marquee-scrim"></div>
                                <div class="logos_marquee-group">
                                    <div class="logos_marquee-logos">
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a02cd268411d94e52_logo1.svg" alt="B2B SaaS software Webflow template logo1" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a76a15350eddcba2b_logo2.svg" alt="B2B SaaS software Webflow template logo2" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a2dea6084e5dbff0c_logo3.svg" alt="B2B SaaS software Webflow template logo3" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53abd213411d2f01ba2_logo4.svg" alt="B2B SaaS software Webflow template logo4" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53abee83c017122e50e_logo5.svg" alt="B2B SaaS software Webflow template logo5" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a76a15350eddcba2f_Logo6.svg" alt="B2B SaaS software Webflow template logo6" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a02cd268411d94e52_logo1.svg" alt="B2B SaaS software Webflow template logo1" class="home2_hero_logos-image"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="logos_marquee-group">
                                    <div class="logos_marquee-logos">
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a02cd268411d94e52_logo1.svg" alt="B2B SaaS software Webflow template logo1" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a76a15350eddcba2b_logo2.svg" alt="B2B SaaS software Webflow template logo2" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a2dea6084e5dbff0c_logo3.svg" alt="B2B SaaS software Webflow template logo3" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53abd213411d2f01ba2_logo4.svg" alt="B2B SaaS software Webflow template logo4" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53abee83c017122e50e_logo5.svg" alt="B2B SaaS software Webflow template logo5" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a76a15350eddcba2f_Logo6.svg" alt="B2B SaaS software Webflow template logo6" class="home2_hero_logos-image"/>
                                        </div>
                                        <div class="logos_marquee-box">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/6836a53a02cd268411d94e52_logo1.svg" alt="B2B SaaS software Webflow template logo1" class="home2_hero_logos-image"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_benefits">
                    <div class="padding-global padding-section-small">
                        <div class="container-default">
                            <div data-w-id="c986fbf3-b9e6-ad42-2cda-26d4387bd7f5" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="section_header is-centered">
                                <div class="section_heading">
                                    <div class="text-style-badge">/ Benefits</div>
                                    <div class="spacer-xxsmall"></div>
                                    <h2>Why B2B SaaS Teams Choose Flowis</h2>
                                </div>
                            </div>
                            <div class="spacer-medium"></div>
                            <div data-w-id="9faa6f1a-38c0-72ae-3a77-972386a2669e" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="home1_benefits_grid">
                                <img src="{{ asset('asset_frontend') }}/images/main_hero1.avif" loading="lazy" id="w-node-_97fbcd01-05f4-4cf2-b6ae-bb7b5204ad5e-4f6e2f6e" alt="B2B SaaS software Webflow template hero1" class="home1_benefits_image"/>
                                <div class="home1_benefits_card">
                                    <img src="{{ asset('asset_frontend') }}/images/benefits_icon1.avif" loading="lazy" alt="B2B SaaS software Webflow template benefits 1" class="icon-height-huge"/>
                                    <div class="spacer-medium"></div>
                                    <div>
                                        <div>Easy to Set Up</div>
                                        <div class="text-style-muted60">Get up and running in less than a day—with no training required</div>
                                    </div>
                                </div>
                                <div class="home1_benefits_card">
                                    <img src="{{ asset('asset_frontend') }}/images/benefits_icon2.avif" loading="lazy" alt="B2B SaaS software Webflow template benefits 2" class="icon-height-huge"/>
                                    <div class="spacer-medium"></div>
                                    <div>
                                        <div>Fast &amp;Lightweight</div>
                                        <div class="text-style-muted60">Lightning-fast UI that reps actually enjoy using—built for speed, simplicity, and zero friction</div>
                                    </div>
                                </div>
                                <div class="home1_benefits_card">
                                    <img src="{{ asset('asset_frontend') }}/images/benefits_icon3.avif" loading="lazy" alt="B2B SaaS software Webflow template benefits 3" class="icon-height-huge"/>
                                    <div class="spacer-medium"></div>
                                    <div>
                                        <div>Secure &amp;Scalable</div>
                                        <div class="text-style-muted60">SOC 2 compliant, built for growing sales teams from 3 to 300+</div>
                                    </div>
                                </div>
                                <div class="home1_benefits_card">
                                    <img src="{{ asset('asset_frontend') }}/images/benefits_icon4.avif" loading="lazy" alt="B2B SaaS software Webflow template benefits4" class="icon-height-huge"/>
                                    <div class="spacer-medium"></div>
                                    <div>
                                        <div>Rep-First Design</div>
                                        <div class="text-style-muted60">Designed with sales reps in mind—for faster adoption and everyday usability</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_features">
                    <div class="padding-global padding-section-medium">
                        <div class="container-default">
                            <div class="home1_features_grid">
                                <div data-w-id="702d7d02-b5ea-c727-64e6-3f0b8fbbaeb0" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="home1_features_left">
                                    <div class="max-width-small">
                                        <div class="text-style-badge">/ Features</div>
                                        <div class="spacer-small"></div>
                                        <h2>All Your SaaS Tools, In One Intuitive Platform</h2>
                                        <div class="spacer-xxsmall"></div>
                                        <div>From pipeline management to sales automation and reporting, Flowis brings everything your team needs into one seamless CRM experience.</div>
                                        <div class="spacer-small"></div>
                                        <a href="/features-v1" class="button w-button">All Features</a>
                                        <div class="spacer-xlarge"></div>
                                        <div class="qoute-box">
                                            <div class="heading-style-h6">Every feature in Flowis solves something we struggled with firsthand—from follow-up chaos to reporting that actually means something.</div>
                                            <div class="spacer-small"></div>
                                            <div class="qoute-author">
                                                <img src="{{ asset('asset_frontend') }}/images/6836ab9027f911d14ce023c7_main%20hero3.avif" loading="lazy" sizes="(max-width: 796px) 100vw, 796px" srcset="{{ asset('asset_frontend') }}/images/6836ab9027f911d14ce023c7_main%20hero3-p-500.avif 500w, {{ asset('asset_frontend') }}/images/6836ab9027f911d14ce023c7_main%20hero3.avif 796w" alt="B2B SaaS software Webflow template hero3" class="icon-height-large"/>
                                                <div>
                                                    <div>Alex Tran</div>
                                                    <div class="text-size-small text-style-muted60">Founder &amp;CEO</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div data-w-id="e2583658-f811-8629-a888-a4a7f6711c9a" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="home1_features_right">
                                    <div class="home1_features_card">
                                        <h3 class="heading-style-h5">Smarter Pipelines</h3>
                                        <div class="spacer-xxsmall"></div>
                                        <div class="text-style-muted60">Build and manage your sales pipeline with a clean, drag-and-drop interface that reps actually enjoy using. Customize every stage to match your sales cycle.</div>
                                        <div class="spacer-xsmall"></div>
                                        <a data-w-id="0f6a1d6b-9f72-a276-9205-3603d5e0005e" href="/features-v1" class="button-arrow w-inline-block">
                                            <div class="text-size-medium">Learn More</div>
                                            <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                            </div>
                                        </a>
                                        <div class="spacer-xsmall"></div>
                                        <img src="{{ asset('asset_frontend') }}/images/6836ac831460235919101153_home1%20feature1.avif" loading="lazy" sizes="(max-width: 1310px) 100vw, 1310px" srcset="{{ asset('asset_frontend') }}/images/6836ac831460235919101153_home1%20feature1-p-500.avif 500w, {{ asset('asset_frontend') }}/images/6836ac831460235919101153_home1%20feature1-p-800.avif 800w, {{ asset('asset_frontend') }}/images/6836ac831460235919101153_home1%20feature1.avif 1310w" alt="B2B SaaS software Webflow template feature1" class="home1_features_image"/>
                                    </div>
                                    <div class="home1_features_card">
                                        <h3 class="heading-style-h5">Sales Automation</h3>
                                        <div class="spacer-xxsmall"></div>
                                        <div class="text-style-muted60">Save time and reduce manual tasks with automation that handles follow-ups, task creation, and deal progression—so your reps can focus on selling</div>
                                        <div class="spacer-xsmall"></div>
                                        <a data-w-id="7f2d5005-0503-0603-91e0-d5cd13215fa2" href="/features-v1" class="button-arrow w-inline-block">
                                            <div class="text-size-medium">Learn More</div>
                                            <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                            </div>
                                        </a>
                                        <div class="spacer-xsmall"></div>
                                        <img src="{{ asset('asset_frontend') }}/images/6836ac360bafc59b3ff5aad5_home1%20feature2.avif" loading="lazy" sizes="(max-width: 1310px) 100vw, 1310px" srcset="{{ asset('asset_frontend') }}/images/6836ac360bafc59b3ff5aad5_home1%20feature2-p-500.avif 500w, {{ asset('asset_frontend') }}/images/6836ac360bafc59b3ff5aad5_home1%20feature2-p-800.avif 800w, {{ asset('asset_frontend') }}/images/6836ac360bafc59b3ff5aad5_home1%20feature2.avif 1310w" alt="B2B SaaS software Webflow template feature2" class="home1_features_image"/>
                                    </div>
                                    <div class="home1_features_card">
                                        <h3 class="heading-style-h5">Forecasting &amp;Reporting</h3>
                                        <div class="spacer-xxsmall"></div>
                                        <div class="text-style-muted60">Track performance, coach reps, and forecast with confidence using real-time insights and out-of-the-box dashboards</div>
                                        <div class="spacer-xsmall"></div>
                                        <a data-w-id="cbcfff4e-8386-8f95-8bf9-8ddf61373365" href="/features-v1" class="button-arrow w-inline-block">
                                            <div class="text-size-medium">Learn More</div>
                                            <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                            </div>
                                        </a>
                                        <div class="spacer-xsmall"></div>
                                        <img src="{{ asset('asset_frontend') }}/images/6836b465d0d417f55f354ecc_home1%20feature3.avif" loading="lazy" sizes="(max-width: 1310px) 100vw, 1310px" srcset="{{ asset('asset_frontend') }}/images/6836b465d0d417f55f354ecc_home1%20feature3-p-500.avif 500w, {{ asset('asset_frontend') }}/images/6836b465d0d417f55f354ecc_home1%20feature3-p-800.avif 800w, {{ asset('asset_frontend') }}/images/6836b465d0d417f55f354ecc_home1%20feature3.avif 1310w" alt="B2B SaaS software Webflow template feature3" class="home1_features_image"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_about">
                    <div class="padding-global padding-section-small">
                        <div class="container-default">
                            <div data-w-id="226c9872-4f08-dadd-cfb6-78ec322c2464" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="home1_about_grid">
                                <img src="{{ asset('asset_frontend') }}/images/6836ae0ff10d1eed5c45277e_home1%20about.avif" loading="lazy" sizes="100vw" srcset="{{ asset('asset_frontend') }}/images/6836ae0ff10d1eed5c45277e_home1%20about-p-500.avif 500w, {{ asset('asset_frontend') }}/images/6836ae0ff10d1eed5c45277e_home1%20about.avif 999w" alt="B2B SaaS software Webflow template about" class="home1_about_image"/>
                                <div class="home1_about_content">
                                    <div class="text-style-badge">/ Why Flowis</div>
                                    <div class="spacer-xxsmall"></div>
                                    <h2>More Visibility. Less Friction. Better Results.</h2>
                                    <div class="spacer-small"></div>
                                    <div>Flowis is designed for B2B sales teams that want speed, simplicity, and full pipeline visibility—without the outdated complexity of traditional CRMs.</div>
                                    <div class="spacer-large"></div>
                                    <div class="list">
                                        <div class="divider"></div>
                                        <div class="list-item">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71095_Checkmark.svg" alt="" class="icon-height-small"/>
                                            <div class="text-size-small">Lightning-fast interface reps actually use</div>
                                        </div>
                                        <div class="divider"></div>
                                        <div class="list-item">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71095_Checkmark.svg" alt="" class="icon-height-small"/>
                                            <div class="text-size-small">Real-time dashboards built for revenue leaders</div>
                                        </div>
                                        <div class="divider"></div>
                                        <div class="list-item">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71095_Checkmark.svg" alt="" class="icon-height-small"/>
                                            <div class="text-size-small">Seamless automation to replace busywork</div>
                                        </div>
                                        <div class="divider"></div>
                                    </div>
                                    <div class="spacer-large"></div>
                                    <a href="/about" class="button w-button">See Flowis in Action</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_line">
                    <div class="padding-section-small">
                        <div data-w-id="7ae3c11f-33bf-6083-35e7-f2658bf6cc0f" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="highlight_marquee">
                            <div class="highlight_marquee-group">
                                <div class="highlight_marquee-texts">
                                    <div class="highlight_marquee-copy">Built for B2B SaaS</div>
                                    <div class="highlight_marquee-copy">*</div>
                                    <div class="highlight_marquee-copy">Enterprise-ready features</div>
                                </div>
                            </div>
                            <div class="highlight_marquee-group">
                                <div class="highlight_marquee-texts">
                                    <div class="highlight_marquee-copy">Built for B2B SaaS</div>
                                    <div class="highlight_marquee-copy">*</div>
                                    <div class="highlight_marquee-copy">Enterprise-ready features</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_industries">
                    <div class="padding-global padding-section-medium">
                        <div class="container-default">
                            <div data-w-id="0050cfcb-3119-10bf-3c3a-e56fd10e7e9c" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="section_header">
                                <div class="section_heading">
                                    <div class="text-style-badge">/ Industries</div>
                                    <div class="spacer-xxsmall"></div>
                                    <h2>Made for B2B Teams in Every Industry</h2>
                                </div>
                                <a href="/industries" class="button w-button">All Industries</a>
                            </div>
                            <div class="spacer-medium"></div>
                            <div data-w-id="e2070ca2-0d4f-1a08-bfca-3b24e4a44a29" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="w-dyn-list">
                                <div role="list" class="industries_grid w-dyn-items">
                                    <div role="listitem" class="w-dyn-item">
                                        <a data-w-id="5f06c15d-b3eb-6faf-31af-e639e13a9151" href="/industry/education" class="industries_card1 w-inline-block">
                                            <div class="industries_icon-box1">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836b802f49a463f053bb076_industries%20icon6.png" loading="lazy" alt="" sizes="100vw" srcset="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836b802f49a463f053bb076_industries%20icon6-p-500.png 500w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836b802f49a463f053bb076_industries%20icon6.png 670w" class="industries_image1"/>
                                                <div style="background-color:rgb(250,250,250)" class="industries_icon-background1"></div>
                                            </div>
                                            <div class="spacer-small"></div>
                                            <div class="text-weight-medium">Education</div>
                                            <div class="text-size-small text-style-muted60">Supporting EdTech platforms, institutions, and training providers in managing outreach</div>
                                            <div class="spacer-xsmall"></div>
                                            <div data-w-id="9bf0a886-3388-b267-d1a5-ef6dfc2d3566" class="button-arrow">
                                                <div class="text-size-medium text-weight-medium">Learn More</div>
                                                <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                    <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div role="listitem" class="w-dyn-item">
                                        <a data-w-id="5f06c15d-b3eb-6faf-31af-e639e13a9151" href="/industry/finance" class="industries_card1 w-inline-block">
                                            <div class="industries_icon-box1">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836b7f56b0fb854b12293d9_industries%20icon5.png" loading="lazy" alt="" sizes="100vw" srcset="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836b7f56b0fb854b12293d9_industries%20icon5-p-500.png 500w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836b7f56b0fb854b12293d9_industries%20icon5.png 670w" class="industries_image1"/>
                                                <div style="background-color:rgb(250,250,250)" class="industries_icon-background1"></div>
                                            </div>
                                            <div class="spacer-small"></div>
                                            <div class="text-weight-medium">Finance</div>
                                            <div class="text-size-small text-style-muted60">Manage B2B deal flow, compliance tracking, and client communication with full pipeline visibility</div>
                                            <div class="spacer-xsmall"></div>
                                            <div data-w-id="9bf0a886-3388-b267-d1a5-ef6dfc2d3566" class="button-arrow">
                                                <div class="text-size-medium text-weight-medium">Learn More</div>
                                                <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                    <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div role="listitem" class="w-dyn-item">
                                        <a data-w-id="5f06c15d-b3eb-6faf-31af-e639e13a9151" href="/industry/manufacturing" class="industries_card1 w-inline-block">
                                            <div class="industries_icon-box1">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836b7e17439aa1e999d9d32_industries%20icon4.png" loading="lazy" alt="" sizes="100vw" srcset="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836b7e17439aa1e999d9d32_industries%20icon4-p-500.png 500w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836b7e17439aa1e999d9d32_industries%20icon4.png 670w" class="industries_image1"/>
                                                <div style="background-color:rgb(250,250,250)" class="industries_icon-background1"></div>
                                            </div>
                                            <div class="spacer-small"></div>
                                            <div class="text-weight-medium">Manufacturing</div>
                                            <div class="text-size-small text-style-muted60">Manage distributor relationships, sales quotes, and field rep activity with CRM tools built for scale</div>
                                            <div class="spacer-xsmall"></div>
                                            <div data-w-id="9bf0a886-3388-b267-d1a5-ef6dfc2d3566" class="button-arrow">
                                                <div class="text-size-medium text-weight-medium">Learn More</div>
                                                <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                    <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_testimonials">
                    <div class="padding-global padding-section-medium">
                        <div class="container-default">
                            <div data-w-id="0d6ddfe1-0aef-40bc-31ae-daefa9c45096" class="section_header is-centered">
                                <div class="section_heading">
                                    <div class="text-style-badge">/ testimonials</div>
                                    <div class="spacer-xxsmall"></div>
                                    <h2>B2B Teams Love Using Flowis—And It Shows</h2>
                                </div>
                            </div>
                            <div class="spacer-medium"></div>
                            <div data-delay="4000" data-animation="fade" class="home1_testimonials_slider w-slider" data-autoplay="true" data-easing="ease-in-out-quart" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-nav-spacing="3" data-duration="500" data-infinite="true">
                                <div class="home1_testimonials_mask w-slider-mask">
                                    <div class="home1_testimonials_slide w-slide">
                                        <div data-w-id="0d6ddfe1-0aef-40bc-31ae-daefa9c450a1" class="home1_testimonials_grid">
                                            <div class="home1_testimonials_grid-left">
                                                <img sizes="100vw" srcset="{{ asset('asset_frontend') }}/images/6836bda90f6de92cfe9caa9a_team%20member2-p-500.avif 500w, {{ asset('asset_frontend') }}/images/6836bda90f6de92cfe9caa9a_team%20member2-p-800.avif 800w, {{ asset('asset_frontend') }}/images/6836bda90f6de92cfe9caa9a_team%20member2-p-1080.avif 1080w, {{ asset('asset_frontend') }}/images/6836bda90f6de92cfe9caa9a_team%20member2.avif 1856w" alt="B2B SaaS software Webflow template team2" src="{{ asset('asset_frontend') }}/images/6836bda90f6de92cfe9caa9a_team%20member2.avif" loading="lazy" class="home1_testimonials_image"/>
                                                <div class="home1_testimonials_highlight hide-mobile-portrait">
                                                    <div class="heading-style-h2">27%</div>
                                                    <div>faster sales cycles</div>
                                                </div>
                                            </div>
                                            <div class="home1_testimonials_grid-right">
                                                <div class="heading-style-h3">“We switched from a bulky legacy CRM to Flowis and immediately saw reps actually using it. It’s intuitive, fast, and has made our pipeline reviews 10x clearer.”</div>
                                                <div class="spacer-large"></div>
                                                <div class="home1_testimonials_author-info">
                                                    <div>Maya Chen</div>
                                                    <div class="text-style-muted60">VP of Sales, RevPilot</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="home1_testimonials_slide w-slide">
                                        <div data-w-id="0d6ddfe1-0aef-40bc-31ae-daefa9c450b3" class="home1_testimonials_grid">
                                            <div class="home1_testimonials_grid-left">
                                                <img sizes="100vw" srcset="{{ asset('asset_frontend') }}/images/6836bdaa16440fdd621b0d1c_team%20member4-p-500.avif 500w, {{ asset('asset_frontend') }}/images/6836bdaa16440fdd621b0d1c_team%20member4-p-800.avif 800w, {{ asset('asset_frontend') }}/images/6836bdaa16440fdd621b0d1c_team%20member4-p-1080.avif 1080w, {{ asset('asset_frontend') }}/images/6836bdaa16440fdd621b0d1c_team%20member4.avif 1856w" alt="B2B SaaS software Webflow template team4" src="{{ asset('asset_frontend') }}/images/6836bdaa16440fdd621b0d1c_team%20member4.avif" loading="lazy" class="home1_testimonials_image"/>
                                                <div class="home1_testimonials_highlight hide-mobile-portrait">
                                                    <div class="heading-style-h2">35%</div>
                                                    <div>lift in productivity</div>
                                                </div>
                                            </div>
                                            <div class="home1_testimonials_grid-right">
                                                <div class="heading-style-h3">Flowis gave our team the visibility we were missing. Our follow-ups are on point, forecasting is finally accurate, and the UI is just smooth.</div>
                                                <div class="spacer-large"></div>
                                                <div class="home1_testimonials_author-info">
                                                    <div>Julian Cross</div>
                                                    <div class="text-style-muted60">Head of RevOps, AxisLoop</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="home1_testimonials_slide w-slide">
                                        <div data-w-id="0d6ddfe1-0aef-40bc-31ae-daefa9c450c5" class="home1_testimonials_grid">
                                            <div class="home1_testimonials_grid-left">
                                                <img sizes="100vw" srcset="{{ asset('asset_frontend') }}/images/6836bda82d21d1845d7ed6dd_team%20member3-p-500.avif 500w, {{ asset('asset_frontend') }}/images/6836bda82d21d1845d7ed6dd_team%20member3-p-800.avif 800w, {{ asset('asset_frontend') }}/images/6836bda82d21d1845d7ed6dd_team%20member3-p-1080.avif 1080w, {{ asset('asset_frontend') }}/images/6836bda82d21d1845d7ed6dd_team%20member3.avif 1856w" alt="B2B SaaS software Webflow template team3" src="{{ asset('asset_frontend') }}/images/6836bda82d21d1845d7ed6dd_team%20member3.avif" loading="lazy" class="home1_testimonials_image"/>
                                                <div class="home1_testimonials_highlight hide-mobile-portrait">
                                                    <div class="heading-style-h2">22%</div>
                                                    <div>fewer missed follow-ups</div>
                                                </div>
                                            </div>
                                            <div class="home1_testimonials_grid-right">
                                                <div class="heading-style-h3">What used to take a week of updates and chasing reps now happens in real time. Flowis keeps our sales team focused and our data clean.</div>
                                                <div class="spacer-large"></div>
                                                <div class="home1_testimonials_author-info">
                                                    <div>Aisha Karim</div>
                                                    <div class="text-style-muted60">Sales Enablement Lead, ClarityOps</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="slider-arrow-v1 is-left w-slider-arrow-left">
                                    <img loading="lazy" src="https://cdn.prod.website-files.com/67f0bb364d93d2a7b94033b2/67f28bc44ed8bfee392530b8_Left.svg" alt=""/>
                                </div>
                                <div class="slider-arrow-v1 is-right w-slider-arrow-right">
                                    <img loading="lazy" src="https://cdn.prod.website-files.com/67f0bb364d93d2a7b94033b2/67f28bc438b9facd4b4e144b_Right.svg" alt=""/>
                                </div>
                                <div class="hide w-slider-nav w-round w-num"></div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_integrations">
                    <div class="padding-global padding-section-medium">
                        <div class="container-default">
                            <div data-w-id="ef0066e1-9b9b-a03b-02ed-131cf1772d5f" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="section_header">
                                <div class="section_heading">
                                    <div class="text-style-badge">/ Integrations</div>
                                    <div class="spacer-xxsmall"></div>
                                    <h2>Works Seamlessly With the Tools You Already Use</h2>
                                </div>
                                <a href="/integrations" class="button w-button">All Integrations</a>
                            </div>
                            <div class="spacer-medium"></div>
                            <div data-w-id="ef0066e1-9b9b-a03b-02ed-131cf1772d69" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="w-dyn-list">
                                <div role="list" class="integrations_grid w-dyn-items">
                                    <div role="listitem" class="w-dyn-item">
                                        <a href="/integration/echoform" class="integrations_card w-inline-block">
                                            <div class="integrations_header">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836c12d5943df78a5d5907f_integration%20icon4.svg" loading="lazy" alt="" class="icon-height-medium"/>
                                                <div class="badge">
                                                    <div class="text-size-tiny text-style-muted60">CRM</div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="spacer-medium"></div>
                                                <div>Echoform</div>
                                                <div class="text-style-muted60">Sync contacts, deals, and activities to keep your sales data aligned across both platforms</div>
                                                <div class="spacer-large"></div>
                                                <div data-w-id="91f4606d-3790-3dfe-47b0-1b8ff407243d" class="button-arrow">
                                                    <div class="text-size-medium text-weight-medium">Learn More</div>
                                                    <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                        <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div role="listitem" class="w-dyn-item">
                                        <a href="/integration/infralyze" class="integrations_card w-inline-block">
                                            <div class="integrations_header">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836c1152ac72e90bff8ba7e_integration%20icon3.svg" loading="lazy" alt="" class="icon-height-medium"/>
                                                <div class="badge">
                                                    <div class="text-size-tiny text-style-muted60">Automation</div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="spacer-medium"></div>
                                                <div>Infralyze</div>
                                                <div class="text-style-muted60">Build custom workflows between Flowis and other platforms with advanced visual automation</div>
                                                <div class="spacer-large"></div>
                                                <div data-w-id="91f4606d-3790-3dfe-47b0-1b8ff407243d" class="button-arrow">
                                                    <div class="text-size-medium text-weight-medium">Learn More</div>
                                                    <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                        <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div role="listitem" class="w-dyn-item">
                                        <a href="/integration/numetron" class="integrations_card w-inline-block">
                                            <div class="integrations_header">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836c0fdffe5f0bd9ba548d8_integration%20icon2.svg" loading="lazy" alt="" class="icon-height-medium"/>
                                                <div class="badge">
                                                    <div class="text-size-tiny text-style-muted60">Scheduling</div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="spacer-medium"></div>
                                                <div>Numetron</div>
                                                <div class="text-style-muted60">Automatically log meetings, attach recordings, and keep call history connected to deals</div>
                                                <div class="spacer-large"></div>
                                                <div data-w-id="91f4606d-3790-3dfe-47b0-1b8ff407243d" class="button-arrow">
                                                    <div class="text-size-medium text-weight-medium">Learn More</div>
                                                    <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                        <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div role="listitem" class="w-dyn-item">
                                        <a href="/integration/triggnet" class="integrations_card w-inline-block">
                                            <div class="integrations_header">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836c0299c1a42cab029798f_integration%20icon1.svg" loading="lazy" alt="" class="icon-height-medium"/>
                                                <div class="badge">
                                                    <div class="text-size-tiny text-style-muted60">Communication</div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="spacer-medium"></div>
                                                <div>Triggnet</div>
                                                <div class="text-style-muted60">Get deal updates, assign tasks, and collaborate with your sales team in real time</div>
                                                <div class="spacer-large"></div>
                                                <div data-w-id="91f4606d-3790-3dfe-47b0-1b8ff407243d" class="button-arrow">
                                                    <div class="text-size-medium text-weight-medium">Learn More</div>
                                                    <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d" class="button is-icon is-secondary">
                                                        <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg" loading="lazy" alt="" class="icon-height-small"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_plans background-color-secondary">
                    <div class="padding-global padding-section-medium">
                        <div class="container-default">
                            <div data-w-id="7725a50a-d4b3-66f1-ffda-0ca245fb89d0" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="section_header is-centered">
                                <div class="section_heading">
                                    <div class="text-style-badge">/ Pricing</div>
                                    <div class="spacer-xxsmall"></div>
                                    <h2>Simple Pricing That Grows With Your Team</h2>
                                    <div class="spacer-xxsmall"></div>
                                    <div>Choose the plan that fits your team’s needs</div>
                                </div>
                            </div>
                            <div class="spacer-medium"></div>
                            <div class="w-dyn-list">
                                <div role="list" class="plan_grid w-dyn-items">
                                    <div role="listitem" class="w-dyn-item">
                                        <div class="plan_box">
                                            <div class="plan_plan-box">
                                                <div class="plan_plan-title">
                                                    <div class="heading-style-h5">Foundation</div>
                                                    <div class="pricing_popular w-condition-invisible">Most Popular</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="home1_pricing_price">
                                                    <div class="heading-style-h2">$29</div>
                                                    <div class="text-style-muted60">per month</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="text-size-small">For startups and small teams ready to get organized and sell smarter</div>
                                                <div class="spacer-small"></div>
                                                <a href="/product/foundation" class="button max-width-full w-button">Get in Touch</a>
                                            </div>
                                            <div class="spacer-small"></div>
                                            <div>What’s included?</div>
                                            <div class="spacer-xsmall"></div>
                                            <div class="text-rich-text text-style-muted60 w-richtext">
                                                <ul role="list">
                                                    <li>Customizable deal pipelines</li>
                                                    <li>Email tracking &amp;activity timeline</li>
                                                    <li>Task reminders and follow-ups</li>
                                                    <li>Basic reports and dashboards</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="plan_box w-condition-invisible">
                                            <div class="plan_plan-box is-dark">
                                                <div class="plan_plan-title">
                                                    <div class="heading-style-h5">Foundation</div>
                                                    <div class="pricing_popular w-condition-invisible">Most Popular</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="home1_pricing_price">
                                                    <div class="heading-style-h2">$29</div>
                                                    <div class="text-style-muted60">per month</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="text-size-small">For startups and small teams ready to get organized and sell smarter</div>
                                                <div class="spacer-small"></div>
                                                <a href="/product/foundation" class="button is-secondary max-width-full w-button">Get in Touch</a>
                                            </div>
                                            <div class="spacer-small"></div>
                                            <div>What’s included?</div>
                                            <div class="spacer-xsmall"></div>
                                            <div class="text-rich-text text-style-muted60 w-richtext">
                                                <ul role="list">
                                                    <li>Customizable deal pipelines</li>
                                                    <li>Email tracking &amp;activity timeline</li>
                                                    <li>Task reminders and follow-ups</li>
                                                    <li>Basic reports and dashboards</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="listitem" class="w-dyn-item">
                                        <div class="plan_box">
                                            <div class="plan_plan-box">
                                                <div class="plan_plan-title">
                                                    <div class="heading-style-h5">Growth</div>
                                                    <div class="pricing_popular">Most Popular</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="home1_pricing_price">
                                                    <div class="heading-style-h2">$59</div>
                                                    <div class="text-style-muted60">per month</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="text-size-small">Perfect for scaling teams that need automation, forecasting, and deeper visibility</div>
                                                <div class="spacer-small"></div>
                                                <a href="/product/growth" class="button max-width-full w-button">Get in Touch</a>
                                            </div>
                                            <div class="spacer-small"></div>
                                            <div>What’s included?</div>
                                            <div class="spacer-xsmall"></div>
                                            <div class="text-rich-text text-style-muted60 w-richtext">
                                                <ul role="list">
                                                    <li>Everything in Foundation</li>
                                                    <li>Workflow automation</li>
                                                    <li>Deal forecasting &amp;weighted pipeline</li>
                                                    <li>Advanced reporting &amp;team analytics</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="plan_box w-condition-invisible">
                                            <div class="plan_plan-box is-dark">
                                                <div class="plan_plan-title">
                                                    <div class="heading-style-h5">Growth</div>
                                                    <div class="pricing_popular">Most Popular</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="home1_pricing_price">
                                                    <div class="heading-style-h2">$59</div>
                                                    <div class="text-style-muted60">per month</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="text-size-small">Perfect for scaling teams that need automation, forecasting, and deeper visibility</div>
                                                <div class="spacer-small"></div>
                                                <a href="/product/growth" class="button is-secondary max-width-full w-button">Get in Touch</a>
                                            </div>
                                            <div class="spacer-small"></div>
                                            <div>What’s included?</div>
                                            <div class="spacer-xsmall"></div>
                                            <div class="text-rich-text text-style-muted60 w-richtext">
                                                <ul role="list">
                                                    <li>Everything in Foundation</li>
                                                    <li>Workflow automation</li>
                                                    <li>Deal forecasting &amp;weighted pipeline</li>
                                                    <li>Advanced reporting &amp;team analytics</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="listitem" class="w-dyn-item">
                                        <div class="plan_box w-condition-invisible">
                                            <div class="plan_plan-box">
                                                <div class="plan_plan-title">
                                                    <div class="heading-style-h5">Enterprise</div>
                                                    <div class="pricing_popular w-condition-invisible">Most Popular</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="home1_pricing_price">
                                                    <div class="heading-style-h2">$149</div>
                                                    <div class="text-style-muted60">per month</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="text-size-small">For large or complex teams needing full control, integrations, and dedicated support</div>
                                                <div class="spacer-small"></div>
                                                <a href="/product/enterprise" class="button max-width-full w-button">Get in Touch</a>
                                            </div>
                                            <div class="spacer-small"></div>
                                            <div>What’s included?</div>
                                            <div class="spacer-xsmall"></div>
                                            <div class="text-rich-text text-style-muted60 w-richtext">
                                                <ul role="list">
                                                    <li>Everything in Growth</li>
                                                    <li>SSO &amp;SOC 2 compliance</li>
                                                    <li>Dedicated account manager</li>
                                                    <li>Custom roles &amp;security policies</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="plan_box">
                                            <div class="plan_plan-box is-dark">
                                                <div class="plan_plan-title">
                                                    <div class="heading-style-h5">Enterprise</div>
                                                    <div class="pricing_popular w-condition-invisible">Most Popular</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="home1_pricing_price">
                                                    <div class="heading-style-h2">$149</div>
                                                    <div class="text-style-muted60">per month</div>
                                                </div>
                                                <div class="spacer-small"></div>
                                                <div class="text-size-small">For large or complex teams needing full control, integrations, and dedicated support</div>
                                                <div class="spacer-small"></div>
                                                <a href="/product/enterprise" class="button is-secondary max-width-full w-button">Get in Touch</a>
                                            </div>
                                            <div class="spacer-small"></div>
                                            <div>What’s included?</div>
                                            <div class="spacer-xsmall"></div>
                                            <div class="text-rich-text text-style-muted60 w-richtext">
                                                <ul role="list">
                                                    <li>Everything in Growth</li>
                                                    <li>SSO &amp;SOC 2 compliance</li>
                                                    <li>Dedicated account manager</li>
                                                    <li>Custom roles &amp;security policies</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_blog">
                    <div class="padding-global padding-section-medium">
                        <div class="container-default">
                            <div data-w-id="9a9187b3-af19-c446-fb57-bdc4c8efbb68" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" class="section_header">
                                <div class="section_heading">
                                    <div class="text-style-badge">/ Blog</div>
                                    <div class="spacer-xxsmall"></div>
                                    <h2>Product Updates &amp;Insights</h2>
                                </div>
                                <a href="/blog" class="button w-button">All Posts</a>
                            </div>
                            <div class="spacer-medium"></div>
                            <div class="w-dyn-list">
                                <div data-w-id="9a9187b3-af19-c446-fb57-bdc4c8efbb73" style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0" role="list" class="blog_grid w-dyn-items">
                                    <div role="listitem" class="w-dyn-item">
                                        <a data-w-id="a77b5212-6650-c3fd-243a-22878cd7838a" href="/blog/behind-the-scenes-how-we-built-flowis-automation" class="blog_card w-inline-block">
                                            <div class="blog_image-wrapper">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cd3c9f5401b054d58d5d_Blog6.jpg" loading="lazy" style="-webkit-transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)" alt="" sizes="100vw" srcset="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cd3c9f5401b054d58d5d_Blog6-p-500.jpg 500w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cd3c9f5401b054d58d5d_Blog6-p-800.jpg 800w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cd3c9f5401b054d58d5d_Blog6-p-1080.jpg 1080w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cd3c9f5401b054d58d5d_Blog6-p-1600.jpg 1600w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cd3c9f5401b054d58d5d_Blog6.jpg 1824w" class="blog_image"/>
                                            </div>
                                            <div style="background-color:rgb(247,247,247)" class="blog_content">
                                                <div>
                                                    <div class="heading-style-h5">Behind the Scenes: How We Built Flowis Automation</div>
                                                    <div class="spacer-xsmall"></div>
                                                    <div class="text-size-small text-style-muted60">A look at the product decisions, challenges, and breakthroughs that shaped our automation engine</div>
                                                    <div class="spacer-small"></div>
                                                </div>
                                                <div class="blog_info">
                                                    <div class="blog_category">CRM Strategy</div>
                                                    <div class="badge">
                                                        <div class="text-size-tiny text-style-muted60">May 28, 2025</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div role="listitem" class="w-dyn-item">
                                        <a data-w-id="a77b5212-6650-c3fd-243a-22878cd7838a" href="/blog/3-sales-metrics-that-actually-matter" class="blog_card w-inline-block">
                                            <div class="blog_image-wrapper">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cce4fc1e0dea0c677299_Blog5.jpg" loading="lazy" style="-webkit-transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)" alt="" sizes="100vw" srcset="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cce4fc1e0dea0c677299_Blog5-p-500.jpg 500w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cce4fc1e0dea0c677299_Blog5-p-800.jpg 800w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cce4fc1e0dea0c677299_Blog5-p-1080.jpg 1080w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cce4fc1e0dea0c677299_Blog5-p-1600.jpg 1600w, https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836cce4fc1e0dea0c677299_Blog5.jpg 1824w" class="blog_image"/>
                                            </div>
                                            <div style="background-color:rgb(247,247,247)" class="blog_content">
                                                <div>
                                                    <div class="heading-style-h5">3 Sales Metrics That Actually Matter
</div>
                                                    <div class="spacer-xsmall"></div>
                                                    <div class="text-size-small text-style-muted60">Cut through the noise. These are the KPIs your team should be tracking weekly inside your CRM</div>
                                                    <div class="spacer-small"></div>
                                                </div>
                                                <div class="blog_info">
                                                    <div class="blog_category">CRM Strategy</div>
                                                    <div class="badge">
                                                        <div class="text-size-tiny text-style-muted60">May 28, 2025</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div role="listitem" class="w-dyn-item">
                                        <a data-w-id="a77b5212-6650-c3fd-243a-22878cd7838a" href="/blog/the-roi-of-crm-simplicity-in-sales-operations" class="blog_card w-inline-block">
                                            <div class="blog_image-wrapper">
                                                <img src="https://cdn.prod.website-files.com/683588d6afb7bd5a9fb70f23/6836ccc7ac580bc4222195b2_Blog4.jpg" loading="lazy" style="-webkit-transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)" alt="" class="blog_image"/>
                                            </div>
                                            <div style="background-color:rgb(247,247,247)" class="blog_content">
                                                <div>
                                                    <div class="heading-style-h5">The ROI of CRM Simplicity in Sales Operations</div>
                                                    <div class="spacer-xsmall"></div>
                                                    <div class="text-size-small text-style-muted60">Explore why less really is more when it comes to CRM design—and how streamlined systems improve productivity</div>
                                                    <div class="spacer-small"></div>
                                                </div>
                                                <div class="blog_info">
                                                    <div class="blog_category">Sales OPS</div>
                                                    <div class="badge">
                                                        <div class="text-size-tiny text-style-muted60">May 28, 2025</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_faq">
                    <div class="padding-global padding-section-medium">
                        <div class="container-default">
                            <div class="faq_grid">
                                <div data-w-id="30290acf-d6d6-e849-11d7-33e63aca85a3" class="faq_left">
                                    <div class="max-width-small">
                                        <div class="text-style-badge">/ FAQ</div>
                                        <div class="spacer-small"></div>
                                        <h2>Everything You Need
to Know—Upfront</h2>
                                        <div class="spacer-xxsmall"></div>
                                        <div>From setup to support and pricing, here are quick answers to the most common questions we get from teams considering Flowis</div>
                                        <div class="spacer-small"></div>
                                        <div class="spacer-xlarge"></div>
                                    </div>
                                    <a href="#" class="faq_video w-inline-block w-lightbox">
                                        <div data-poster-url="{{ asset('asset_frontend') }}/images%2F6836a28bd68c9a29a6160ae4_Untitled%20design%20%284%29-poster-00001.jpg" data-video-urls="{{ asset('asset_frontend') }}/images%2F6836a28bd68c9a29a6160ae4_Untitled%20design%20%284%29-transcode.mp4,{{ asset('asset_frontend') }}/images%2F6836a28bd68c9a29a6160ae4_Untitled%20design%20%284%29-transcode.webm" data-autoplay="true" data-loop="true" data-wf-ignore="true" class="faq_video-play w-background-video w-background-video-atom">
                                            <video id="30290acf-d6d6-e849-11d7-33e63aca85b0-video" autoplay="" loop="" style="background-image:url(&quot;{{ asset('asset_frontend') }}/images%2F6836a28bd68c9a29a6160ae4_Untitled%20design%20%284%29-poster-00001.jpg&quot;)" muted="" playsinline="" data-wf-ignore="true" data-object-fit="cover">
                                                <source src="{{ asset('asset_frontend') }}/images%2F6836a28bd68c9a29a6160ae4_Untitled%20design%20%284%29-transcode.mp4" data-wf-ignore="true"/>
                                                <source src="{{ asset('asset_frontend') }}/images%2F6836a28bd68c9a29a6160ae4_Untitled%20design%20%284%29-transcode.webm" data-wf-ignore="true"/>
                                            </video>
                                        </div>
                                        <div class="play">
                                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71067_Play.svg" alt="" class="icon-height-small"/>
                                            <div class="text-color-alternate">Watch Demo</div>
                                        </div>
                                        <script type="application/json" class="w-json">
                                            {
                                                "items": [
                                                    {
                                                        "url": "https://www.youtube.com/watch?v=fSQaE6Dcr-s&ab_channel=MBTRMUSIK",
                                                        "originalUrl": "https://www.youtube.com/watch?v=fSQaE6Dcr-s&ab_channel=MBTRMUSIK",
                                                        "width": 940,
                                                        "height": 528,
                                                        "thumbnailUrl": "https://i.ytimg.com/vi/fSQaE6Dcr-s/hqdefault.jpg",
                                                        "html": "<iframe class=\"embedly-embed\" src=\"//cdn.embedly.com/widgets/media.html?src=https%3A%2F%2Fwww.youtube.com%2Fembed%2FfSQaE6Dcr-s%3Ffeature%3Doembed&display_name=YouTube&url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DfSQaE6Dcr-s&image=https%3A%2F%2Fi.ytimg.com%2Fvi%2FfSQaE6Dcr-s%2Fhqdefault.jpg&type=text%2Fhtml&schema=youtube\" width=\"940\" height=\"528\" scrolling=\"no\" title=\"YouTube embed\" frameborder=\"0\" allow=\"autoplay; fullscreen; encrypted-media; picture-in-picture;\" allowfullscreen=\"true\"></iframe>",
                                                        "type": "video"
                                                    }
                                                ],
                                                "group": ""
                                            }</script>
                                    </a>
                                </div>
                                <div data-w-id="30290acf-d6d6-e849-11d7-33e63aca85b5" class="faq_box">
                                    <div data-w-id="25c4837b-3530-41a4-d13b-6cd7954f94bc" class="faq_accordion">
                                        <div class="faq_question">
                                            <div class="faq_question_header">
                                                <div>Is there a free trial?</div>
                                            </div>
                                            <div class="faq_icon">
                                                <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71090_Plus%204.svg" alt="" class="icon-height-small"/>
                                            </div>
                                        </div>
                                        <div class="faq_answer">
                                            <div class="faq_answer-wrapper">
                                                <div class="max-width-large">
                                                    <p class="text-size-small text-style-muted60">Yes! Every Flowis plan starts with a 14-day free trial—no credit card required.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-w-id="25c4837b-3530-41a4-d13b-6cd7954f94bc" class="faq_accordion">
                                        <div class="faq_question">
                                            <div class="faq_question_header">
                                                <div>How long does setup take?</div>
                                            </div>
                                            <div class="faq_icon">
                                                <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71090_Plus%204.svg" alt="" class="icon-height-small"/>
                                            </div>
                                        </div>
                                        <div class="faq_answer">
                                            <div class="faq_answer-wrapper">
                                                <div class="max-width-large">
                                                    <p class="text-size-small text-style-muted60">Most teams are fully onboarded within a day. No technical training or consultants needed.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-w-id="25c4837b-3530-41a4-d13b-6cd7954f94bc" class="faq_accordion">
                                        <div class="faq_question">
                                            <div class="faq_question_header">
                                                <div>Can I integrate Flowis with my current tools?</div>
                                            </div>
                                            <div class="faq_icon">
                                                <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71090_Plus%204.svg" alt="" class="icon-height-small"/>
                                            </div>
                                        </div>
                                        <div class="faq_answer">
                                            <div class="faq_answer-wrapper">
                                                <div class="max-width-large">
                                                    <p class="text-size-small text-style-muted60">Absolutely. Flowis connects with Gmail, Slack, Google Calendar, Zapier, HubSpot, and more out of the box.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-w-id="25c4837b-3530-41a4-d13b-6cd7954f94bc" class="faq_accordion">
                                        <div class="faq_question">
                                            <div class="faq_question_header">
                                                <div>What happens after the trial ends?</div>
                                            </div>
                                            <div class="faq_icon">
                                                <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71090_Plus%204.svg" alt="" class="icon-height-small"/>
                                            </div>
                                        </div>
                                        <div class="faq_answer">
                                            <div class="faq_answer-wrapper">
                                                <div class="max-width-large">
                                                    <p class="text-size-small text-style-muted60">You’ll choose a plan to continue. All your data stays intact—nothing is lost or reset.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-w-id="25c4837b-3530-41a4-d13b-6cd7954f94bc" class="faq_accordion">
                                        <div class="faq_question">
                                            <div class="faq_question_header">
                                                <div>Is Flowis secure enough for enterprise use?</div>
                                            </div>
                                            <div class="faq_icon">
                                                <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71090_Plus%204.svg" alt="" class="icon-height-small"/>
                                            </div>
                                        </div>
                                        <div class="faq_answer">
                                            <div class="faq_answer-wrapper">
                                                <div class="max-width-large">
                                                    <p class="text-size-small text-style-muted60">Yes, Flowis is SOC 2 Type II compliant and supports role-based access, audit logging, and data encryption.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-w-id="25c4837b-3530-41a4-d13b-6cd7954f94bc" class="faq_accordion">
                                        <div class="faq_question">
                                            <div class="faq_question_header">
                                                <div>What kind of support do you offer?</div>
                                            </div>
                                            <div class="faq_icon">
                                                <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71090_Plus%204.svg" alt="" class="icon-height-small"/>
                                            </div>
                                        </div>
                                        <div class="faq_answer">
                                            <div class="faq_answer-wrapper">
                                                <div class="max-width-large">
                                                    <p class="text-size-small text-style-muted60">All plans include live chat support. Growth and Enterprise plans get priority email support and onboarding assistance.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section class="section_home1_banner">
                    <div class="padding-global padding-section-small">
                        <div class="container-default">
                            <div data-w-id="feeb4b2f-d334-fc2c-ee55-86ad3c99dc52" class="home1_banner_wrapper">
                                <div class="text-style-badge text-color-white">/ ALL-IN-ONE</div>
                                <div class="spacer-small"></div>
                                <h2>Power Pipeline Performance. Drive Revenue Faster</h2>
                                <div class="spacer-xsmall"></div>
                                <div class="text-style-muted80">Bring clarity to your entire sales process—track deals, automate follow-ups, and close with confidence in one purpose-built platform</div>
                                <div class="spacer-small"></div>
                                <div class="button-group">
                                    <a href="/contact-v1" class="button is-secondary w-button">Get Started</a>
                                    <a href="/utility/demo" class="button is-outline w-button">Book a Demo</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


            </main>
            <footer class="footer">
                <div class="container-default">
                    <div class="footer-box">
                        <div class="footer_banners">
                            <div class="footer_buy">
                                <div class="heading-style-h4 text-color-alternate text-align-center">A sleek, top-tier template crafted for 
 B2B SaaS platforms, sales teams, and growth-focused tech startups</div>
                                <a href="https://webflow.com/templates/html/flowis-website-template" target="_blank" class="button is-secondary w-button">Buy RoomGate</a>
                            </div>
                            <img src="{{ asset('asset_frontend') }}/images/6835a437638964759ac06caa_footer%20image.avif" loading="lazy" sizes="(max-width: 1374px) 100vw, 1374px" srcset="{{ asset('asset_frontend') }}/images/6835a437638964759ac06caa_footer%20image-p-500.avif 500w, {{ asset('asset_frontend') }}/images/6835a437638964759ac06caa_footer%20image-p-800.avif 800w, {{ asset('asset_frontend') }}/images/6835a437638964759ac06caa_footer%20image.avif 1374w" alt="B2B SaaS software Webflow template" class="footer_image"/>
                        </div>
                        <div class="spacer-large"></div>
                        <nav class="footer_nav">
                            <div class="footer_nav-column">
                                <div class="text-style-badge">Multilayout</div>
                                <div class="footer_nav-list">
                                    <a href="/home-v1" aria-current="page" class="footer_nav-link w--current">Home V1</a>
                                    <a href="/home-v2" class="footer_nav-link">Home V2</a>
                                    <a href="/home-v3" class="footer_nav-link">Home V3</a>
                                    <a href="/features-v1" class="footer_nav-link">Features V1</a>
                                    <a href="/features-v2" class="footer_nav-link">Features V2</a>
                                    <a href="/features-v3" class="footer_nav-link">Features V3</a>
                                    <a href="/contact-v1" class="footer_nav-link">Contact V1</a>
                                    <a href="/contact-v2" class="footer_nav-link">Contact V2</a>
                                    <a href="/contact-v3" class="footer_nav-link">Contact V3</a>
                                </div>
                            </div>
                            <div class="footer_nav-column">
                                <div class="text-style-badge">Company</div>
                                <div class="footer_nav-list">
                                    <a href="/industries" class="footer_nav-link">Industries</a>
                                    <a href="/industry/technology" class="footer_nav-link">Industry Solution</a>
                                    <a href="/integrations" class="footer_nav-link">Integrations</a>
                                    <a href="/integration/blipify" class="footer_nav-link">Integration Details</a>
                                    <a href="/about" class="footer_nav-link">About</a>
                                    <a href="/category/plans" class="footer_nav-link">Pricing</a>
                                    <a href="/product/enterprise" class="footer_nav-link">Plan</a>
                                    <a href="/blog" class="footer_nav-link">Blog</a>
                                    <a href="/blog/how-to-migrate-to-flowis-in-under-a-day" class="footer_nav-link">Blog Post</a>
                                    <a href="/api" class="footer_nav-link">API</a>
                                </div>
                            </div>
                            <div class="footer_nav-column">
                                <div class="text-style-badge">Account &amp;Utility</div>
                                <div class="footer_nav-list">
                                    <a href="/utility/terms" class="footer_nav-link">Terms &amp;Conditions</a>
                                    <a href="/account/sign-in" class="footer_nav-link">Sign in</a>
                                    <a href="/account/sign-up" class="footer_nav-link">Sign up</a>
                                    <a href="/account/forgot-password" class="footer_nav-link">Forgot Password</a>
                                    <a href="/utility/demo" class="footer_nav-link">Demo</a>
                                    <a href="/404" class="footer_nav-link">404</a>
                                    <a href="/401" class="footer_nav-link">Password Protected</a>
                                </div>
                            </div>
                            <div class="footer_nav-column">
                                <div class="text-style-badge">Template</div>
                                <div class="footer_nav-list">
                                    <a href="/template/licenses" class="footer_nav-link">Licenses</a>
                                    <a href="/template/style-guide" class="footer_nav-link">Style Guide</a>
                                    <a href="/template/changelog" class="footer_nav-link">Changelog</a>
                                    <a href="https://webflow.com/templates/designers/loonis" target="_blank" class="footer_nav-link">More Templates</a>
                                </div>
                                <div class="w-form">
                                    <form id="wf-form-Newsletter-2" name="wf-form-Newsletter-2" data-name="Newsletter" method="get" data-wf-page-id="68369d6ecd4dbc0b4f6e2f6e" data-wf-element-id="9ed03d61-974b-c35b-395d-9d86fe960ffb">
                                        <label for="Email-Address-3" class="form_label">Subscribe to our newsletter</label>
                                        <div class="div-block">
                                            <input class="form_input is-footer w-input" maxlength="256" name="Email-Address" data-name="Email Address" placeholder="Email Address" type="email" id="Email-Address-3" required=""/>
                                            <input type="submit" data-wait="Please wait..." class="button is-footer w-button" value="Send"/>
                                        </div>
                                    </form>
                                    <div class="form_message-success w-form-done">
                                        <div>Thank you! Your submission has been received!</div>
                                    </div>
                                    <div class="form_message-error w-form-fail">
                                        <div>Oops! Something went wrong while submitting the form.</div>
                                    </div>
                                </div>
                            </div>
                        </nav>
                        <div class="spacer-large"></div>
                        <div class="footer_info">
                            <div class="text-size-tiny">
                                © Copyright 2025 Designs by <a href="https://www.loonis.co/" target="_blank" class="text-size-tiny">Loonis</a>
                                , powered by <a href="https://webflow.com/" target="_blank" class="text-size-tiny">Webflow</a>
                            </div>
                            <div class="footer_socials">
                                <a href="#" class="social-link w-inline-block">
                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb70f4f_linkedin.svg" alt="linkedin" class="social-link-image"/>
                                </a>
                                <a href="#" class="social-link w-inline-block">
                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb70f56_instagram.svg" alt="instagram" class="social-link-image"/>
                                </a>
                                <a href="#" class="social-link w-inline-block">
                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb70f46_twitter.svg" alt="twitter" class="social-link-image"/>
                                </a>
                            </div>
                        </div>
                        <div class="spacer-xsmall"></div>
                        <div class="divider"></div>
                        <div class="spacer-xsmall"></div>
                        <div class="text-size-tiny text-color-secondary">This is a legal disclaimer for website footers. It should begin with a statement confirming the company’s official registration, including a placeholder for the location and a sample registration number—for instance, “Incorporated in [Location], USA (Reg. No. YY-123456).” The disclaimer should also include a note about the company’s regulatory authorization, referencing a relevant oversight body and legislation. You may use placeholders like “Licensed by the [State Regulatory Authority] under the [Relevant State Act] (License No. YY-123456).”</div>
                    </div>
                </div>
            </footer>
            <div class="bottom-blur hide-mobile-landscape">
                <div class="w-embed">
                    <style>
                        .bottom-blur {
                            backdrop-filter: blur(7px);
                            background-image: linear-gradient(#fff0, #ffffff00 90%, #fff);
                            mask-image: linear-gradient(#0000,10%,#fff 40% 100%);
                            pointer-events: none;
                        }
                    </style>
                </div>
            </div>
        </div>
        <script src="{{ asset('asset_frontend') }}/js/jquery-3.5.1.min.js" type="text/javascript" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        <script src="{{ asset('asset_frontend') }}/js/webflow.schunk.bfd5c8b822744376.js" type="text/javascript"></script>
        <script src="{{ asset('asset_frontend') }}/js/webflow.schunk.7d96b44c939f450f.js" type="text/javascript"></script>
        <script src="{{ asset('asset_frontend') }}/js/webflow.js" type="text/javascript"></script>
    </body>
</html>
