<?php
/**
 * The header template file
 *
 * @package TrendToday
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ["Prompt", "sans-serif"],
                },
                colors: {
                    primary: "#1a1a1a",
                    accent: "#FF4500",
                    "news-tech": "#3B82F6",
                    "news-ent": "#EC4899",
                    "news-fin": "#10B981",
                    "news-sport": "#F59E0B",
                },
                typography: {
                    DEFAULT: {
                        css: {
                            maxWidth: 'none',
                            color: '#374151',
                            lineHeight: '1.75',
                            fontSize: '1.125rem',
                            'h1, h2, h3, h4, h5, h6': {
                                color: '#111827',
                                fontWeight: '700',
                                lineHeight: '1.2',
                            },
                            'p': {
                                marginTop: '1.25em',
                                marginBottom: '1.25em',
                            },
                            'a': {
                                color: '#FF4500',
                                textDecoration: 'underline',
                                '&:hover': {
                                    color: '#dc3a00',
                                },
                            },
                            'strong': {
                                fontWeight: '600',
                                color: '#111827',
                            },
                            'ul, ol': {
                                marginTop: '1.25em',
                                marginBottom: '1.25em',
                                paddingLeft: '1.625em',
                            },
                            'li': {
                                marginTop: '0.5em',
                                marginBottom: '0.5em',
                            },
                            'blockquote': {
                                borderLeftColor: '#FF4500',
                                borderLeftWidth: '4px',
                                paddingLeft: '1em',
                                fontStyle: 'italic',
                                color: '#6B7280',
                            },
                            'img': {
                                borderRadius: '0.5rem',
                                marginTop: '2em',
                                marginBottom: '2em',
                            },
                        },
                    },
                },
            }
        },
        plugins: [
            function({ addComponents, theme }) {
                addComponents({
                    '.prose': {
                        '& p': {
                            marginTop: theme('spacing.5'),
                            marginBottom: theme('spacing.5'),
                            lineHeight: theme('lineHeight.relaxed'),
                        },
                        '& h1, & h2, & h3, & h4, & h5, & h6': {
                            marginTop: theme('spacing.6'),
                            marginBottom: theme('spacing.4'),
                            fontWeight: theme('fontWeight.bold'),
                            lineHeight: theme('lineHeight.tight'),
                            color: theme('colors.gray.900'),
                        },
                        '& h1': { fontSize: theme('fontSize.3xl[0]') },
                        '& h2': { fontSize: theme('fontSize.2xl[0]') },
                        '& h3': { fontSize: theme('fontSize.xl[0]') },
                        '& a': {
                            color: theme('colors.accent'),
                            textDecoration: 'underline',
                            '&:hover': {
                                color: theme('colors.orange.700'),
                            },
                        },
                        '& strong': {
                            fontWeight: theme('fontWeight.semibold'),
                            color: theme('colors.gray.900'),
                        },
                        '& ul, & ol': {
                            marginTop: theme('spacing.5'),
                            marginBottom: theme('spacing.5'),
                            paddingLeft: theme('spacing.6'),
                        },
                        '& li': {
                            marginTop: theme('spacing.2'),
                            marginBottom: theme('spacing.2'),
                        },
                        '& blockquote': {
                            borderLeftWidth: '4px',
                            borderLeftColor: theme('colors.accent'),
                            paddingLeft: theme('spacing.4'),
                            fontStyle: 'italic',
                            color: theme('colors.gray.600'),
                            marginTop: theme('spacing.6'),
                            marginBottom: theme('spacing.6'),
                        },
                        '& img': {
                            borderRadius: theme('borderRadius.lg'),
                            marginTop: theme('spacing.8'),
                            marginBottom: theme('spacing.8'),
                            maxWidth: '100%',
                            height: 'auto',
                        },
                        '& code': {
                            backgroundColor: theme('colors.gray.100'),
                            padding: theme('spacing.1'),
                            borderRadius: theme('borderRadius.sm'),
                            fontSize: theme('fontSize.sm[0]'),
                        },
                        '& pre': {
                            backgroundColor: theme('colors.gray.900'),
                            color: theme('colors.gray.100'),
                            padding: theme('spacing.4'),
                            borderRadius: theme('borderRadius.lg'),
                            overflow: 'auto',
                            marginTop: theme('spacing.6'),
                            marginBottom: theme('spacing.6'),
                        },
                    },
                });
            },
        ],
    }
    </script>
    <?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen' ); ?>>
<?php wp_body_open(); ?>

<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 focus:z-50 focus:px-4 focus:py-2 focus:bg-accent focus:text-white"><?php _e( 'Skip to content', 'trendtoday' ); ?></a>

<?php
// Show navbar on all pages
// (front-page.php is now disabled, so we show navbar everywhere)
get_template_part( 'template-parts/navbar' );
?>
