<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sc="http://www.niso.org/schemas/sushi/counter" xmlns:c="http://www.niso.org/schemas/counter">
<xsl:output method="text" encoding="UTF-8" />

<xsl:template match="/">

    <xsl:text>&#10;</xsl:text>

    <xsl:for-each select="sc:ReportResponse/sc:Report/c:Report/c:Customer/c:ReportItems/c:ItemPerformance">

      <xsl:value-of select="../c:ItemIdentifier[c:Type='Proprietary']/c:Value"/>
      <xsl:text>,</xsl:text>
      <xsl:value-of select="c:Period/c:Begin"/>
      <xsl:text>,</xsl:text>
      <xsl:value-of select="c:Period/c:End"/>
      <xsl:text>,</xsl:text>
      <xsl:value-of select="c:Category" />
      <xsl:text>,</xsl:text>
      <xsl:value-of select="c:Instance/c:MetricType" />
      <xsl:text>,</xsl:text>
      <xsl:value-of select="c:Instance/c:Count" />
      <xsl:text>&#10;</xsl:text>

    </xsl:for-each>

</xsl:template>
</xsl:stylesheet>

