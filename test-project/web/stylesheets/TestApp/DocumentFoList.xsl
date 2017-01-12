<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:obj="urn:ru:ilb:meta:TestApp:Document"
	xmlns:req="urn:ru:ilb:meta:TestApp:DocumentListRequest"
	xmlns:res="urn:ru:ilb:meta:TestApp:DocumentListResponse"
	exclude-result-prefixes="xsl fo req res obj"
	version="1.0">

	<xsl:output method="xml" encoding="UTF-8" indent="yes"/>

	<xsl:strip-space elements="*" />	

		<xsl:template match="res:DocumentListResponse">
			<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format" font-family="Liberation Serif" font-size="10pt" language="en">
				<fo:layout-master-set>
					<fo:simple-page-master master-name="A4Form" page-height="29.7cm" page-width="21cm" margin="1cm 1.5cm 1cm 1.5cm">
						<fo:region-body />
					</fo:simple-page-master>
				</fo:layout-master-set>
				<fo:page-sequence master-reference="A4Form" initial-page-number="1">
					<fo:flow flow-name="xsl-region-body" font-size="8pt" font-family="Liberation Serif">
						<fo:block-container width="125mm" border-bottom-style="solid" border-width="thin" space-after="2mm">
								
							<xsl:variable name="req" select="req:DocumentListRequest"/>
							
							<fo:block space-after="2mm">
											Документы с
									<xsl:value-of select="req:DocumentListRequest/req:dateStart"/> по
									<xsl:value-of select="req:DocumentListRequest/req:dateEnd"/>
									<xsl:if test="$req/req:strHas != ''">
									, которые содержат «<xsl:value-of select="req:DocumentListRequest/req:strHas"/>» в наименовании документа.
									</xsl:if>
							</fo:block>
							
							<xsl:if test="$req/following-sibling::*">
								<fo:table table-layout="fixed" width="200mm">
									
									<fo:table-column column-width="20mm"/>
									<fo:table-column column-width="100mm"/>
									<fo:table-column/>
									<fo:table-column/>
									
									<fo:table-body>
										<fo:table-row>
											<fo:table-cell text-align="left">
												<fo:block font-weight="bold">Дата</fo:block>
											</fo:table-cell>
											<fo:table-cell>
												<fo:block font-weight="bold">Наименование</fo:block>
											</fo:table-cell>
											<fo:table-cell>
												<fo:block font-weight="bold">Ключевые слова</fo:block>
											</fo:table-cell>
											<fo:table-cell>
												<fo:block font-weight="bold">Удален</fo:block>
											</fo:table-cell>
										</fo:table-row>
										<xsl:for-each select="obj:Document">
											<fo:table-row>
												<fo:table-cell>
													<fo:block>
														<xsl:value-of select="obj:docDate"/>
													</fo:block>
												</fo:table-cell>
												<fo:table-cell>
													<fo:block>
														<fo:basic-link external-destination="http://testapp/web/document.php?objectId-0={obj:objectId}">
															<fo:inline text-decoration="underline"><xsl:value-of select="obj:displayName"/></fo:inline>
														</fo:basic-link>
													</fo:block>
												</fo:table-cell>
												<fo:table-cell>
													<fo:block>
														<xsl:value-of select="obj:keywords"/>
													</fo:block>
												</fo:table-cell>
												<fo:table-cell>
													<fo:block>
														<xsl:choose>
															<xsl:when test="obj:deleted = 'true'">
																Да
															</xsl:when>
															<xsl:otherwise>
																Нет
															</xsl:otherwise>
														</xsl:choose>
													</fo:block>
												</fo:table-cell>
											</fo:table-row>
										</xsl:for-each>
									</fo:table-body>
								</fo:table>
							</xsl:if>
						</fo:block-container>
					</fo:flow>
				</fo:page-sequence>
			</fo:root>
		</xsl:template>
</xsl:stylesheet>