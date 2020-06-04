<?php
/**
 * Sitemaps: WP_Sitemaps_Stylesheet class
 *
 * This class provides the XSL stylesheets to style all sitemaps.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Stylesheet provider class.
 *
 * @since 5.5.0
 */
class WP_Sitemaps_Stylesheet {
	/**
	 * Renders the xsl stylesheet depending on whether its the sitemap index or not.
	 *
	 * @param string $type Stylesheet type. Either 'sitemap' or 'index'.
	 */
	public function render_stylesheet( $type ) {
		header( 'Content-type: application/xml; charset=UTF-8' );

		if ( 'sitemap' === $type ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All content escaped below.
			echo $this->get_sitemap_stylesheet();
		}

		if ( 'index' === $type ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All content escaped below.
			echo $this->get_sitemap_index_stylesheet();
		}

		exit;
	}

	/**
	 * Returns the escaped xsl for all sitemaps, except index.
	 *
	 * @since 5.5.0
	 */
	public function get_sitemap_stylesheet() {
		$css           = $this->get_stylesheet_css();
		$title         = esc_xml( __( 'XML Sitemap', 'core-sitemaps' ) );
		$sitemaps_link = sprintf(
			/* translators: %s: URL to sitemaps documentation. */
			'<a href="%s">sitemaps.org</a>',
			esc_url( __( 'https://www.sitemaps.org/', 'core-sitemaps' ) )
		);
		$description   = sprintf(
			/* translators: %s: link to sitemaps documentation. */
			esc_xml( __( 'This XML Sitemap is generated by WordPress to make your content more visible for search engines. Learn more about XML sitemaps on %s.', 'core-sitemaps' ) ),
			$sitemaps_link
		);
		$text          = sprintf(
			/* translators: %s: number of URLs. */
			esc_xml( __( 'Number of URLs in this XML Sitemap: %s.', 'core-sitemaps' ) ),
			'<xsl:value-of select="count( sitemap:urlset/sitemap:url )" />'
		);

		$lang       = get_language_attributes( 'html' );
		$url        = esc_xml( __( 'URL', 'core-sitemaps' ) );
		$lastmod    = esc_xml( __( 'Last Modified', 'core-sitemaps' ) );
		$changefreq = esc_xml( __( 'Change Frequency', 'core-sitemaps' ) );
		$priority   = esc_xml( __( 'Priority', 'core-sitemaps' ) );

		$xsl_content = <<<XSL
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
		version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
		exclude-result-prefixes="sitemap"
		>

	<xsl:output method="html" encoding="UTF-8" indent="yes"/>

	<!--
	  Set variables for whether lastmod, changefreq or priority occur for any url in the sitemap.
	  We do this up front because it can be expensive in a large sitemap.
	  -->
	<xsl:variable name="has-lastmod"    select="count( /sitemap:urlset/sitemap:url/sitemap:lastmod )"    />
	<xsl:variable name="has-changefreq" select="count( /sitemap:urlset/sitemap:url/sitemap:changefreq )" />
	<xsl:variable name="has-priority"   select="count( /sitemap:urlset/sitemap:url/sitemap:priority )"   />

	<xsl:template match="/">
		<html {$lang}>
			<head>
				<title>{$title}</title>
				<style>{$css}</style>
			</head>
			<body>
				<div id="sitemap__header">
					<h1>{$title}</h1>
					<p>{$description}</p>
				</div>
				<div id="sitemap__content">
					<p class="text">{$text}</p>
					<table id="sitemap__table">
						<thead>
							<tr>
								<th class="loc">{$url}</th>
								<xsl:if test="\$has-lastmod">
									<th class="lastmod">{$lastmod}</th>
								</xsl:if>
								<xsl:if test="\$has-changefreq">
									<th class="changefreq">{$changefreq}</th>
								</xsl:if>
								<xsl:if test="\$has-priority">
									<th class="priority">{$priority}</th>
								</xsl:if>
							</tr>
						</thead>
						<tbody>
							<xsl:for-each select="sitemap:urlset/sitemap:url">
								<tr>
									<td class="loc"><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc" /></a></td>
									<xsl:if test="\$has-lastmod">
										<td class="lastmod"><xsl:value-of select="sitemap:lastmod" /></td>
									</xsl:if>
									<xsl:if test="\$has-changefreq">
										<td class="changefreq"><xsl:value-of select="sitemap:changefreq" /></td>
									</xsl:if>
									<xsl:if test="\$has-priority">
										<td class="priority"><xsl:value-of select="sitemap:priority" /></td>
									</xsl:if>
								</tr>
							</xsl:for-each>
						</tbody>
					</table>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>

XSL;

		/**
		 * Filters the content of the sitemap stylesheet.
		 *
		 * @since 5.5.0
		 *
		 * @param string $xsl Full content for the xml stylesheet.
		 */
		return apply_filters( 'wp_sitemaps_stylesheet_content', $xsl_content );
	}

	/**
	 * Returns the escaped xsl for the index sitemaps.
	 *
	 * @since 5.5.0
	 */
	public function get_sitemap_index_stylesheet() {
		$css           = $this->get_stylesheet_css();
		$title         = esc_xml( __( 'XML Sitemap', 'core-sitemaps' ) );
		$sitemaps_link = sprintf(
			/* translators: %s: URL to sitemaps documentation. */
			'<a href="%s">sitemaps.org</a>',
			esc_url( __( 'https://www.sitemaps.org/', 'core-sitemaps' ) )
		);
		$description  = sprintf(
			/* translators: %s: link to sitemaps documentation. */
			esc_xml( __( 'This XML Sitemap is generated by WordPress to make your content more visible for search engines. Learn more about XML sitemaps on %s.', 'core-sitemaps' ) ),
			$sitemaps_link
		);
		$text         = sprintf(
			/* translators: %s: number of URLs. */
			esc_xml( __( 'Number of URLs in this XML Sitemap: %s.', 'core-sitemaps' ) ),
			'<xsl:value-of select="count( sitemap:sitemapindex/sitemap:sitemap )" />'
		);

		$lang    = get_language_attributes( 'html' );
		$url     = esc_xml( __( 'URL', 'core-sitemaps' ) );
		$lastmod = esc_xml( __( 'Last Modified', 'core-sitemaps' ) );

		$xsl_content = <<<XSL
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
		version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
		exclude-result-prefixes="sitemap"
		>

	<xsl:output method="html" encoding="UTF-8" indent="yes" />

	<!--
	  Set variables for whether lastmod occurs for any sitemap in the index.
	  We do this up front because it can be expensive in a large sitemap.
	  -->
	<xsl:variable name="has-lastmod" select="count( /sitemap:sitemapindex/sitemap:sitemap/sitemap:lastmod )" />

	<xsl:template match="/">
		<html {$lang}>
			<head>
				<title>{$title}</title>
				<style>{$css}</style>
			</head>
			<body>
				<div id="sitemap__header">
					<h1>{$title}</h1>
					<p>{$description}</p>
				</div>
				<div id="sitemap__content">
					<p class="text">{$text}</p>
					<table id="sitemap__table">
						<thead>
							<tr>
								<th class="loc">{$url}</th>
								<xsl:if test="\$has-lastmod">
									<th class="lastmod">{$lastmod}</th>
								</xsl:if>
							</tr>
						</thead>
						<tbody>
							<xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
								<tr>
									<td class="loc"><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc" /></a></td>
									<xsl:if test="\$has-lastmod">
										<td class="lastmod"><xsl:value-of select="sitemap:lastmod" /></td>
									</xsl:if>
								</tr>
							</xsl:for-each>
						</tbody>
					</table>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>

XSL;

		/**
		 * Filters the content of the sitemap index stylesheet.
		 *
		 * @since 5.5.0
		 *
		 * @param string $xsl Full content for the xml stylesheet.
		 */
		return apply_filters( 'wp_sitemaps_index_stylesheet_content', $xsl_content );
	}

	/**
	 * Gets the CSS to be included in sitemap XSL stylesheets.
	 *
	 * @since 5.5.0
	 *
	 * @return string The CSS.
	 */
	public function get_stylesheet_css() {
		$css = '
			body {
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
				color: #444;
			}

			#sitemap__table {
				border: solid 1px #ccc;
				border-collapse: collapse;
			}

			#sitemap__table tr th {
				text-align: left;
			}

			#sitemap__table tr td,
			#sitemap__table tr th {
				padding: 10px;
			}

			#sitemap__table tr:nth-child(odd) td {
				background-color: #eee;
			}

			a:hover {
				text-decoration: none;
			}';

		/**
		 * Filters the css only for the sitemap stylesheet.
		 *
		 * @since 5.5.0
		 *
		 * @param string $css CSS to be applied to default xsl file.
		 */
		return apply_filters( 'wp_sitemaps_stylesheet_css', $css );
	}
}
