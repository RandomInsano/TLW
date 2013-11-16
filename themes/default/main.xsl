<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<!-- Transformer hints. Robots in disguise. -->
	<xsl:output method="html" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" />
	<xsl:output indent="yes" />

	<!-- The identity. Lets us copy HTML verbatum from the source document -->
	<xsl:template match="node()|@*">
		<xsl:copy>
			<xsl:apply-templates select="node()|@*"/>
		</xsl:copy>
	</xsl:template>

	<!-- Our actual document -->
	<xsl:template match="/document">
		<html>
			<head>
				<xsl:for-each select="meta/style">
					<link rel="stylesheet" type="text/css">
						<xsl:attribute name="href">
							<xsl:value-of select="file" />
						</xsl:attribute>
					</link>
				</xsl:for-each>
				<title><xsl:value-of select="title"/></title>
			</head>
			<body>
				<h1>
				<xsl:value-of select="title"/>
				</h1>
				<xsl:apply-templates select="body"/>
				<div id="docinfo">
					<xsl:apply-templates select="meta"/>
				</div>
			</body>
		</html>
	</xsl:template>
	<xsl:template match="meta">
		Written by <em><xsl:value-of select="author"/></em> on <xsl:value-of select="date"/>.
	</xsl:template>
</xsl:stylesheet>
