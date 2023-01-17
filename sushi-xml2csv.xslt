<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sc="http://www.niso.org/schemas/sushi/counter" xmlns:c="http://www.niso.org/schemas/counter">
<xsl:output method="text" encoding="UTF-8" />

<xsl:template match="/">
    <xsl:text>Institution, ISSN, Begin, End, Category, MetricType, Count</xsl:text>
    <xsl:text>&#xA;</xsl:text>
    <xsl:variable name="journal" select="sc:ReportResponse/sc:Report/c:Report/c:Customer/c:ReportItems/c:ItemIdentifier[c:Type='Proprietary']/c:Value" />
    <xsl:for-each select="sc:ReportResponse/sc:Report/c:Report/c:Customer/c:ReportItems/c:ItemPerformance">
        <xsl:value-of select="$journal"/>,<xsl:value-of select="c:Period/c:Begin"/>,<xsl:value-of select="c:Period/c:End"/>,<xsl:value-of select="c:Category" />,<xsl:value-of select="c:Instance/c:MetricType" />,<xsl:value-of select="c:Instance/c:Count" />
        <xsl:if test="position() != last()">
            <xsl:text>&#xA;</xsl:text>
        </xsl:if>
    </xsl:for-each>
</xsl:template>
</xsl:stylesheet>

