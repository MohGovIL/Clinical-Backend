<?php

namespace ImportData\Plugin;

class DummyXML {

/*
'country',                     'moh_country',                  'countries'
'gender',                     'sex',                          'gender',
'language',                   'language',                     'language'
'city',                       'mh_cities',                    'city',
'Street',                     'mh_streets',                   'street'
'death_situation',            'mh_reason_of_death',           'death'
'ExtendedLogicalStatus',      'mh_medic_approval',            'elsList'
'SocialSecurityOffices',      'mh_ss_branches',               'sso'
'HMO',                        'mh_ins_organizations',         'hmo'
'title',                      'abook_type',                   'title'
'ICD_typusere',               'occurrence',                   'frequency'
'expertise',                  'physician_type',               'expertise'
*/
    public function getXML($tableName){

        return $this->{'dummy_response_'.$tableName};
    }
    //response for  GetChangTableDate request
    public function getXmlForGetChangTable($type = true){

        if($type) {
            return "<s:Envelope
                        xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">
                        <s:Body>
                            <GetChangTableDateResponse
                                xmlns=\"http://www.health.gov.il\">
                                <GetChangTableDateResult
                                    xmlns:a=\"http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.CentralHub\"
                                    xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">
                                    <a:ListblCodesChanges>
                                        <a:HBTblCodesChanges>
                                            <a:AllRecordsTbl>1408</a:AllRecordsTbl>
                                            <a:ChangedDate
                                                xmlns:b=\"http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common\">
                                                <b:Day>16</b:Day>
                                                <b:HebrowDate></b:HebrowDate>
                                                <b:Month>6</b:Month>
                                                <b:Year>2014</b:Year>
                                            </a:ChangedDate>
                                            <a:Name>city</a:Name>
                                        </a:HBTblCodesChanges>
                                    </a:ListblCodesChanges>
                                </GetChangTableDateResult>
                                <pResult>CompletSuccessfully</pResult>
                            </GetChangTableDateResponse>
                        </s:Body>
                    </s:Envelope>";

        }else{

            return "<s:Envelope
                        xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">
                        <s:Body>
                            <GetChangTableDateResponse
                                xmlns=\"http://www.health.gov.il\">
                                <GetChangTableDateResult
                                    xmlns:a=\"http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.CentralHub\"
                                    xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">
                                    <a:ListblCodesChanges/>
                                </GetChangTableDateResult>
                                <pResult>DataNotFound</pResult>
                            </GetChangTableDateResponse>
                        </s:Body>
                    </s:Envelope>";

        }
    }

    public $dummy_response_country = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                        <country>
                            <Code>901</Code>
                            <Description>-</Description>
                            <Continent_code>0</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>99</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>317</Code>
                            <Description>אבחזיסטאן</Description>
                            <Continent_code>4</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>98</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>254</Code>
                            <Description>אוגנדה</Description>
                            <Description_eng>UGANDA </Description_eng>
                            <Continent_code>5</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>UG</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>315</Code>
                            <Description>אוזבקיסטן</Description>
                            <Description_eng>UZBEKISTAN </Description_eng>
                            <Continent_code>2</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>UZ</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>510</Code>
                            <Description>אוסטריה</Description>
                            <Description_eng>AUSTRIA </Description_eng>
                            <Continent_code>3</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>AT</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>860</Code>
                            <Description>אוסטרליה</Description>
                            <Description_eng>AUSTRALIA</Description_eng>
                            <Continent_code>8</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>AU</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>305</Code>
                            <Description>אוקראינה</Description>
                            <Description_eng>UKRAINE </Description_eng>
                            <Continent_code>4</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>UA</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>820</Code>
                            <Description>אורגוואי</Description>
                            <Description_eng>URUGUAY </Description_eng>
                            <Continent_code>7</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>98</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>741</Code>
                            <Description>אזור תעלת פנמה</Description>
                            <Continent_code>7</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>98</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>311</Code>
                            <Description>אזרביג\'אן</Description>
                            <Description_eng>AZERBAIJAN</Description_eng>
                            <Continent_code>4</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>98</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>64</Code>
                            <Description>איחוד האמירויות הערב</Description>
                            <Description_eng>UNITED ARAB EMIRATES</Description_eng>
                            <Continent_code>2</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>98</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>670</Code>
                            <Description>איטליה</Description>
                            <Description_eng>ITALY</Description_eng>
                            <Continent_code>3</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>IT</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>885</Code>
                            <Description>איי גילברט ואליס</Description>
                            <Continent_code>8</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>98</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country>
                        <country>
                            <Code>738</Code>
                            <Description>איי הבתולה</Description>
                            <Description_eng>VIRGIN ISLANDS</Description_eng>
                            <Continent_code>7</Continent_code>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <Country_merkava_code>98</Country_merkava_code>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date>
                        </country> 
					</BroadCastTransDataSet>;
					</a:XMLData>
					<a:XMLschema>
						
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_city = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                         <city><Code>168</Code><Description>כפר יונה</Description> <Description_eng>KEFAR YONA</Description_eng> <District_code>41</District_code> <Health_district_code>41</Health_district_code> <X_cordinate>194199.999936531</X_cordinate> <Y_cordinate>691700.000000443</Y_cordinate><From_date>2016-01-06T00:00:00+02:00</From_date><Municipality_status>0</Municipality_status> <Create_date>2016-01-06T11:41:20.193+02:00</Create_date> <Update_date>2016-01-06T11:41:20.193+02:00</Update_date></city> 
                         <city><Code>224</Code>  <Description>שושנת העמקים (רסקו)</Description> <Description_eng>SHOSHANNAT HAAMAQIM</Description_eng> <District_code>41</District_code> <Health_district_code>41</Health_district_code><X_cordinate>186700.000057546</X_cordinate><Y_cordinate>695700.000004169</Y_cordinate><From_date>1951-01-01T00:00:00+02:00</From_date><To_date>2016-01-05T00:00:00+02:00</To_date> <Municipality_status>16</Municipality_status><Create_date>2000-01-01T00:00:00+02:00</Create_date><Update_date>2016-01-06T11:41:20.187+02:00</Update_date></city> 
                         <city><Code>224</Code> <Description>שושנת העמקים</Description><Description_eng>SHOSHANNAT HAAMAQIM</Description_eng> <District_code>41</District_code> <Health_district_code>41</Health_district_code><X_cordinate>186700.000057546</X_cordinate> <Y_cordinate>695700.000004169</Y_cordinate><From_date>2016-01-06T00:00:00+02:00</From_date><Municipality_status>16</Municipality_status> <Create_date>2016-01-06T11:41:20.193+02:00</Create_date><Update_date>2016-01-06T11:41:20.193+02:00</Update_date></city> 
                         <city> <Code>476</Code> <Description>אם אל גנם</Description><District_code>23</District_code> <Health_district_code>23</Health_district_code><From_date>1948-01-01T00:00:00+02:00</From_date><To_date>2003-12-31T00:00:00+02:00</To_date><Create_date>2000-01-01T00:00:00+02:00</Create_date><Update_date>2016-01-06T11:41:20.2+02:00</Update_date></city> 
                         <city> <Code>484</Code> <Description>גדידה</Description><District_code>24</District_code> <Health_district_code>24</Health_district_code><From_date>1948-01-01T00:00:00+02:00</From_date><To_date>2003-12-31T00:00:00+02:00</To_date><Create_date>2000-01-01T00:00:00+02:00</Create_date><Update_date>2016-01-06T11:41:20.2+02:00</Update_date></city> 
                         <city> <Code>494</Code> <Description>דאלית אל-כרמל</Description><Description_eng>\'DALIYAT AL-KARMEL</Description_eng><District_code>31</District_code><Health_district_code>31</Health_district_code><X_cordinate>205599.999947148</X_cordinate> <Y_cordinate>734400.000040211</Y_cordinate><From_date>2009-01-01T00:00:00+02:00</From_date><To_date>2016-01-05T00:00:00+02:00</To_date><Municipality_status>99</Municipality_status><Create_date>2009-01-01T00:00:00+02:00</Create_date><Update_date>2016-01-06T11:41:20.187+02:00</Update_date></city> 
                         <city> <Code>494</Code> <Description>דאלית אל-כרמל</Description><Description_eng>DALIYAT AL-KARMEL</Description_eng> <District_code>31</District_code><Health_district_code>31</Health_district_code><X_cordinate>205599.999947148</X_cordinate><Y_cordinate>734400.000040211</Y_cordinate><From_date>2016-01-06T00:00:00+02:00</From_date><Municipality_status>99</Municipality_status> <Create_date>2016-01-06T11:41:20.193+02:00</Create_date> <Update_date>2016-01-06T11:41:20.193+02:00</Update_date></city> 
                         <city> 
                         <Code>502</Code> 
                         <Description>גיי"א</Description> 
                         <District_code>24</District_code> 
                         <Health_district_code>24</Health_district_code> 
                         <X_cordinate>220199.999960746</X_cordinate> 
                         <Y_cordinate>762099.999938007</Y_cordinate> 
                         <From_date>2003-09-01T00:00:00+02:00</From_date> 
                         <To_date>2016-01-05T00:00:00+02:00</To_date> 
                         <Municipality_status>99</Municipality_status> 
                         <Create_date>2003-09-01T00:00:00+02:00</Create_date> 
                         <Update_date>2016-01-06T11:41:20.187+02:00</Update_date> 
                         </city> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';

    public $dummy_response_death = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                     <death_situation> <Code>1</Code> <Description>רצח</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>2</Code> <Description>תאונת עבודה</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>3</Code> <Description>מוות פתאומי</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>4</Code> <Description>תאונת דרכים</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>5</Code> <Description>התאבדות</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>6</Code> <Description>טביעה</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>7</Code> <Description>שריפה</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>8</Code> <Description>חומר נפץ</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>9</Code> <Description>הרעלת גז</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_SocialSecurityOffices = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                                           <s:Body>
                                              <GenerationXMLDataTableResponse xmlns="http://www.health.gov.il">
                                                 <GenerationXMLDataTableResult xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                                    <a:XMLData><![CDATA[<BroadCastTransDataSet> 
                                          <SocialSecurityOffices> 
                                            <Code>3800</Code> 
                                            <City>7100</City> 
                                            <Citydesc /> 
                                            <Address>רחוב צה"ל 4, אשדוד</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2900</Code> 
                                            <City>7100</City> 
                                            <Citydesc /> 
                                            <Address>רחוב הנשיא 101, אשקלון</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2999</Code> 
                                            <City>1031</City> 
                                            <Citydesc /> 
                                            <Address>רחוב בן יהודה מספר 21, שדרות</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5200</Code> 
                                            <City>9000</City> 
                                            <Citydesc /> 
                                            <Address>בניין פריזמה, רחוב שזר 31 א\', באר שבע</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5253</Code> 
                                            <City>31</City> 
                                            <Citydesc /> 
                                            <Address>רחוב קיבוץ גלויות מספר 1, אופקים</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5258</Code> 
                                            <City>2600</City> 
                                            <Citydesc /> 
                                            <Address>בניין הוכמן, רחוב מדיין מספר 12, אילת</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5255</Code> 
                                            <City>2200</City> 
                                            <Citydesc /> 
                                            <Address>רחוב ז\'בוטינסקי מספר 1,  דימונה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5256</Code> 
                                            <City>831</City> 
                                            <Citydesc /> 
                                            <Address>רחוב אליהו הנביא 1, ירוחם</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5257</Code> 
                                            <City>99</City> 
                                            <Citydesc /> 
                                            <Address>רחוב נחל ציחור מספר 2, מצפה רמון</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5254</Code> 
                                            <City>246</City> 
                                            <Citydesc /> 
                                            <Address>רחוב הגפן 23 במרכז המסחרי לב נוי, נתיבות</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5259</Code> 
                                            <City>2560</City> 
                                            <Citydesc /> 
                                            <Address>אזור התעשיה 9, ערד</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5263</Code> 
                                            <Citydesc>ערערה בנגב</Citydesc> 
                                            <Address>בכניסה לישוב ליד מד"א</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5251</Code> 
                                            <City>1161</City> 
                                            <Citydesc /> 
                                            <Address>מרכז המסחרי 909, מאחורי העירייה, רהט</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5262</Code> 
                                            <City>1286</City> 
                                            <Citydesc /> 
                                            <Address>צמוד למשרדי הלשכה לשירותים חברתיים</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5260</Code> 
                                            <City>1303</City> 
                                            <Citydesc /> 
                                            <Address>ניידת שירות החונה ברחבה ליד משרדי הלשכה לשירותים חברתיים</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5261</Code> 
                                            <City>1059</City> 
                                            <Citydesc /> 
                                            <Address>מרכז הכפר, צמוד לקופת חולים מכבי</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>5264</Code> 
                                            <City>1060</City> 
                                            <Citydesc /> 
                                            <Address>שכונה 3 מגרש 910</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>3700</Code> 
                                            <City>6100</City> 
                                            <Citydesc /> 
                                            <Address>רחוב אהרונוביץ 12 בני ברק</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>3785</Code> 
                                            <City>1309</City> 
                                            <Citydesc /> 
                                            <Address>מרכז יוסף, רחוב שמעיה 19, אלעד   </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>1700</Code> 
                                            <City>6500</City> 
                                            <Citydesc /> 
                                            <Address>רחוב הלל יפה 7 א, חדרה.</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>1764</Code> 
                                            <City>2710</City> 
                                            <Citydesc /> 
                                            <Address>רחוב ראשי, אום אל פאחם </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>1763</Code> 
                                            <City>1020</City> 
                                            <Citydesc /> 
                                            <Address>רחוב הנשיא, אור עקיבא (ליד בנק הפועלים) </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>1761</Code> 
                                            <City>7800</City> 
                                            <Citydesc /> 
                                            <Address>דרך הבנים 21, מרכז מסחרי, פרדס חנה   </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>1600</Code> 
                                            <City>6600</City> 
                                            <Citydesc /> 
                                            <Address>רחוב פנחס לבון 26, חולון</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>1000</Code> 
                                            <City>4000</City> 
                                            <Citydesc /> 
                                            <Address>שדרות פלי"ם 8, חיפה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>1080</Code> 
                                            <City>494</City> 
                                            <Citydesc /> 
                                            <Address>רחוב 1 בית 35, דלית אל-כרמל </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>1078</Code> 
                                            <City>2100</City> 
                                            <Citydesc /> 
                                            <Address>מרכז מסחרי, טירת הכרמל</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>100</Code> 
                                            <City>6700</City> 
                                            <Citydesc /> 
                                            <Address>רחוב הופיין 1, טבריה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>114</Code> 
                                            <City>2034</City> 
                                            <Citydesc /> 
                                            <Address>רחוב רסקו מספר 1, חצור הגלילית</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>113</Code> 
                                            <Citydesc>מראר</Citydesc> 
                                            <Address>כפר מראר</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>104</Code> 
                                            <City>8000</City> 
                                            <Citydesc /> 
                                            <Address>רחוב הפלמ"ח 100, צפת</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>111</Code> 
                                            <City>4100</City> 
                                            <Citydesc /> 
                                            <Address>סוסיתא 1 קצרין </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>115</Code> 
                                            <City>2800</City> 
                                            <Citydesc /> 
                                            <Address>שדרות תל חי, מרכז מסחרי, קרית שמונה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>3100</Code> 
                                            <City>5000</City> 
                                            <Citydesc /> 
                                            <Address>רחוב התקומה מספר 30, יפו</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>3132</Code> 
                                            <City>6200</City> 
                                            <Citydesc /> 
                                            <Address>רחוב ז\'בוטינסקי 2, בת ים</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>4100</Code> 
                                            <City>3000</City> 
                                            <Citydesc /> 
                                            <Address>בניין בן סירא/ בניין בן שטח</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>4182</Code> 
                                            <City>3574</City> 
                                            <Citydesc /> 
                                            <Address>המועצה המקומית, תורה ועבודה, בית אל </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>4148</Code> 
                                            <City>3780</City> 
                                            <Citydesc /> 
                                            <Address>רחוב רבי עקיבא מספר 17, ביתר עילית</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>4145</Code> 
                                            <City>3000</City> 
                                            <Citydesc /> 
                                            <Address>רחוב איבן בטוטא 5, מזרח ירושלים </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>4142</Code> 
                                            <City>3616</City> 
                                            <Citydesc /> 
                                            <Address>רחוב הר מיכוור מספר 12, מעלה אדומים</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>4143</Code> 
                                            <City>3608</City> 
                                            <Citydesc /> 
                                            <Address>מרכז המועצה המקומית מעלה אפרים</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>4184</Code> 
                                            <City>3765</City> 
                                            <Citydesc /> 
                                            <Address>לב הקניון, מועצה איזורית בנימין</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>4120</Code> 
                                            <City>3617</City> 
                                            <Citydesc /> 
                                            <Address>מרכז מסחרי עפרה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>4144</Code> 
                                            <City>3611</City> 
                                            <Citydesc /> 
                                            <Address>מרכז מסחרי (ליד בנק לאומי) </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2300</Code> 
                                            <City>6900</City> 
                                            <Citydesc /> 
                                            <Address>רחוב ויצמן 39, כפר סבא</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2327</Code> 
                                            <City>6400</City> 
                                            <Citydesc /> 
                                            <Address>שדרות בן גוריון 22, הרצליה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2303</Code> 
                                            <City>3640</City> 
                                            <Citydesc /> 
                                            <Address>שד\' רחבעם זאבי, קניון קרני השומרון </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>3500</Code> 
                                            <City>1139</City> 
                                            <Citydesc /> 
                                            <Address>שדרות  נשיאי ישראל 11, כרמיאל</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>3501</Code> 
                                            <Citydesc>כרמיאל קניון "כיכר העיר" </Citydesc> 
                                            <Address>שדרות נשיאי ישראל מספר 3, קניון "כיכר העיר" </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>600</Code> 
                                            <City>9100</City> 
                                            <Citydesc /> 
                                            <Address>רחוב ויצמן 62, נהריה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>650</Code> 
                                            <City>502</City> 
                                            <Citydesc /> 
                                            <Address>אזור תעשיה ירכא   </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>649</Code> 
                                            <City>1063</City> 
                                            <Citydesc /> 
                                            <Address>הרב קוק 86, מעלות תרשיחא   </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>646</Code> 
                                            <City>7600</City> 
                                            <Citydesc /> 
                                            <Address>גיבורי סיני 4, עכו</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>500</Code> 
                                            <City>7300</City> 
                                            <Citydesc /> 
                                            <Address>רח המחצבות 3 , אזור התעשייה, נצרת.</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>574</Code> 
                                            <City>509</City> 
                                            <Citydesc /> 
                                            <Address>רח\' בני ביתך 1 בניין סראייא מוקארי כפר כנא </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>571</Code> 
                                            <City>1061</City> 
                                            <Citydesc /> 
                                            <Address>רחוב עמל 1, אזור התעשייה ב\' נצרת עילית</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>572</Code> 
                                            <City>8800</City> 
                                            <Citydesc /> 
                                            <Address>רחוב התאנה, אזור תעשייה, שפרעם   </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>573</Code> 
                                            <Citydesc>תרדיון</Citydesc> 
                                            <Address>הרחוב האדום 1, אזור התעשייה משגב   </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2100</Code> 
                                            <City>7400</City> 
                                            <Citydesc /> 
                                            <Address>רחוב הרצל 68, נתניה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2122</Code> 
                                            <City>2730</City> 
                                            <Citydesc /> 
                                            <Address>מוחמד דרוויש מול המסגד - טייבה  </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>200</Code> 
                                            <City>7700</City> 
                                            <Citydesc /> 
                                            <Address>רחוב מנחם אוסישקין 2, עפולה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>268</Code> 
                                            <City>9200</City> 
                                            <Citydesc /> 
                                            <Address>רחוב ירושלים הבירה 9, בית שאן </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>266</Code> 
                                            <City>240</City> 
                                            <Citydesc /> 
                                            <Address>רחוב התמר 6 ,מרכז מסחרי חדש יקנעם עילית   </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>267</Code> 
                                            <City>874</City> 
                                            <Citydesc /> 
                                            <Address>רח ניצנים 39, מגדל העמק</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2400</Code> 
                                            <City>7900</City> 
                                            <Citydesc /> 
                                            <Address>רחוב רוטשילד 72, פתח תקווה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2407</Code> 
                                            <City>3570</City> 
                                            <Citydesc /> 
                                            <Address>רחוב אורי בר און מספר 2 , אריאל</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2408</Code> 
                                            <City>9400</City> 
                                            <Citydesc /> 
                                            <Address>רחוב מרבד הקסמים מספר 6 בנין העירייה  יהוד</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2479</Code> 
                                            <City>3660</City> 
                                            <Citydesc /> 
                                            <Address>מרכז מסחרי מספר 6 , עמנואל</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2489</Code> 
                                            <City>2640</City> 
                                            <Citydesc /> 
                                            <Address>רחוב העבודה מספר 11 , ראש העין</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>900</Code> 
                                            <City>4991</City> 
                                            <Citydesc /> 
                                            <Address>רחוב אח"י אילת 50 קרית חיים</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>918</Code> 
                                            <City>6800</City> 
                                            <Citydesc /> 
                                            <Address>רחוב העצמאות 59 קרית אתא </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2800</Code> 
                                            <City>8300</City> 
                                            <Citydesc /> 
                                            <Address>רחוב ישראל גלילי 7, ראשון לציון</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2600</Code> 
                                            <City>8400</City> 
                                            <Citydesc /> 
                                            <Address>רחוב רמז 64, רחובות</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2683</Code> 
                                            <City>2660</City> 
                                            <Citydesc /> 
                                            <Address>שדרות דואני 20, קניון לב יבנה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2696</Code> 
                                            <City>1034</City> 
                                            <Citydesc /> 
                                            <Address>רש"י 1, פסג\' אורן, קריית מלאכי</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2697</Code> 
                                            <City>2630</City> 
                                            <Citydesc /> 
                                            <Address>רחוב שד\' העצמאות 64 , קרית גת</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2500</Code> 
                                            <City>8500</City> 
                                            <Citydesc /> 
                                            <Address>רחוב דני 9, רמלה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2540</Code> 
                                            <City>2610</City> 
                                            <Citydesc /> 
                                            <Address>הנשיא 3 בית שמש</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2534</Code> 
                                            <City>1200</City> 
                                            <Citydesc /> 
                                            <Address>קניון עזריאלי, בניין יהונתן קומה 5, מודיעין</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2512</Code> 
                                            <City>3797</City> 
                                            <Citydesc /> 
                                            <Address>אבני נזר 46 מרכז מסחרי מודיעין עילית</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>2533</Code> 
                                            <City>1304</City> 
                                            <Citydesc /> 
                                            <Address>בנין המועצה המקומית חבל מודיעין   </Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>3900</Code> 
                                            <City>8600</City> 
                                            <Citydesc /> 
                                            <Address>רחוב החשמונאים 15, רמת גן</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>3636</Code> 
                                            <City>2400</City> 
                                            <Citydesc /> 
                                            <Address>הרצל 81 אור יהודה</Address> 
                                          </SocialSecurityOffices> 
                                          <SocialSecurityOffices> 
                                            <Code>3000</Code> 
                                            <City>5000</City> 
                                            <Citydesc /> 
                                            <Address>רחוב יצחק שדה 17, תל אביב</Address> 
                                          </SocialSecurityOffices> 
                                        </BroadCastTransDataSet>]]></a:XMLData>
                                                    <a:XMLschema><![CDATA[<?xml version="1.0" encoding="utf-16"?> 
                                        <xs:schema id="BroadCastTransDataSet" xmlns="" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata"> 
                                          <xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true"> 
                                            <xs:complexType> 
                                              <xs:choice minOccurs="0" maxOccurs="unbounded"> 
                                                <xs:element name="SocialSecurityOffices"> 
                                                  <xs:complexType> 
                                                    <xs:sequence> 
                                                      <xs:element name="Code" type="xs:int" minOccurs="0" /> 
                                                      <xs:element name="City" type="xs:string" minOccurs="0" /> 
                                                      <xs:element name="Citydesc" type="xs:string" minOccurs="0" /> 
                                                      <xs:element name="Address" type="xs:string" minOccurs="0" /> 
                                                      <xs:element name="From_date" type="xs:dateTime" minOccurs="0" /> 
                                                      <xs:element name="To_date" type="xs:dateTime" minOccurs="0" /> 
                                                      <xs:element name="Create_date" type="xs:dateTime" minOccurs="0" /> 
                                                      <xs:element name="Update_date" type="xs:dateTime" minOccurs="0" /> 
                                                    </xs:sequence> 
                                                  </xs:complexType> 
                                                </xs:element> 
                                              </xs:choice> 
                                            </xs:complexType> 
                                          </xs:element> 
                                        </xs:schema>]]></a:XMLschema>
                                                 </GenerationXMLDataTableResult>
                                                 <pResult>CompletSuccessfully</pResult>
                                              </GenerationXMLDataTableResponse>
                                           </s:Body>
                                        </s:Envelope>';


    public $dummy_response_family_status = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                        <family_status> 
                            <Code>10</Code> 
                            <Description>רווק/ה</Description> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                            <Code_btl>1</Code_btl> 
                          </family_status> 
                          <family_status> 
                            <Code>11</Code> 
                            <Description>ידוע/ה בציבור</Description> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                          </family_status> 
                          <family_status> 
                            <Code>20</Code> 
                            <Description>נשוי/אה</Description> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                            <Code_btl>2</Code_btl> 
                          </family_status> 
                          <family_status> 
                            <Code>21</Code> 
                            <Description>נשוי/נפרד</Description> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                          </family_status> 
                          <family_status> 
                            <Code>22</Code> 
                            <Description>נשוי לבן זוג אחר</Description> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                          </family_status> 
                          <family_status> 
                            <Code>30</Code> 
                            <Description>גרוש/ה</Description> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                            <Code_btl>3</Code_btl> 
                          </family_status> 
                          <family_status> 
                            <Code>40</Code> 
                            <Description>אלמן/נה</Description> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                            <Code_btl>4</Code_btl> 
                          </family_status> 
                          <family_status> 
                            <Code>99</Code> 
                            <Description>לא ידוע</Description> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                            <Code_btl>5</Code_btl> 
                          </family_status> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';



    public $dummy_response_death_situation = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                     <death_situation> <Code>1</Code> <Description>רצח</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>2</Code> <Description>תאונת עבודה</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>3</Code> <Description>מוות פתאומי</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>4</Code> <Description>תאונת דרכים</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>5</Code> <Description>התאבדות</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>6</Code> <Description>טביעה</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>7</Code> <Description>שריפה</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>8</Code> <Description>חומר נפץ</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation> 
                     <death_situation> <Code>9</Code> <Description>הרעלת גז</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </death_situation>
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_street = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                    <street> <Street_code>127</Street_code> <City_code>476</City_code> <Street_desc>תירוש</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>101</Street_code> <City_code>476</City_code> <Street_desc>אורה</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>102</Street_code> <City_code>484</City_code> <Street_desc>סמ השיטה 2</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>103</Street_code> <City_code>484</City_code> <Street_desc>סמ שיזף הגדול </Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>104</Street_code> <City_code>484</City_code> <Street_desc>סמ דקל דום</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>105</Street_code> <City_code>502</City_code> <Street_desc>סמ האשל</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>106</Street_code> <City_code>21</City_code> <Street_desc>משעול אוכם</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>107</Street_code> <City_code>21</City_code> <Street_desc>משעול סיתוונית</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>108</Street_code> <City_code>21</City_code> <Street_desc>משעול פיקוס</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>109</Street_code> <City_code>21</City_code> <Street_desc>משעול דרדר</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>110</Street_code> <City_code>21</City_code> <Street_desc>משעול דוחן</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>111</Street_code> <City_code>21</City_code> <Street_desc>משעול כוכב</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>112</Street_code> <City_code>21</City_code> <Street_desc>משעול לענה</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
                    <street> <Street_code>113</Street_code> <City_code>21</City_code> <Street_desc>משעול סהרון</Street_desc> <From_date>1948-01-01T00:00:00+02:00</From_date> <Create_date>1948-01-01T00:00:00+02:00</Create_date> <Update_date>1948-01-01T00:00:00+02:00</Update_date> </street> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_title = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                    <title> <Code>1</Code> <Description>גב\'</Description> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </title> 
                    <title> <Code>2</Code> <Description>ד"ר</Description> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </title> 
                    <title> <Code>3</Code> <Description>מר</Description> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </title> 
                    <title><Code>4</Code> <Description>עו"ד</Description> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </title> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_expertise = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                        <expertise> <Code>0</Code> <Description>ללא התמחות</Description> <Profession_code>2</Profession_code> <From_date>2016-01-28T00:00:00+02:00</From_date> <Create_date>2016-01-28T16:18:17.77+02:00</Create_date> <Update_date>2016-01-28T16:18:17.77+02:00</Update_date> </expertise> 
                        <expertise> <Code>0</Code> <Description>ללא התמחות</Description> <Profession_code>3</Profession_code> <From_date>2016-01-28T00:00:00+02:00</From_date> <Create_date>2016-01-28T16:18:17.77+02:00</Create_date> <Update_date>2016-01-28T16:18:17.77+02:00</Update_date> </expertise> 
                        <expertise> <Code>0</Code> <Description>ללא התמחות</Description> <Profession_code>4</Profession_code> <From_date>2016-01-28T00:00:00+02:00</From_date> <Create_date>2016-01-28T16:18:17.77+02:00</Create_date> <Update_date>2016-01-28T16:18:17.77+02:00</Update_date> </expertise> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';

    public $dummy_response_HMO = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                        <HMO> <Code>1</Code> <Description>כללית</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </HMO> 
                        <HMO> <Code>2</Code> <Description>לאומית</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </HMO> 
                        <HMO> <Code>3</Code> <Description>מכבי</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </HMO> 
                        <HMO> <Code>4</Code> <Description>מאוחדת</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </HMO> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_icd_type = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                    <icd_type> <Code>0</Code> <Description>לא רשום</Description> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </icd_type> 
                    <icd_type> <Code>1</Code> <Description>אבחנה ראשית</Description> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </icd_type> 
                    <icd_type> <Code>2</Code> <Description>אבחנה משנית</Description> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </icd_type> 
                    <icd_type> <Code>3</Code> <Description>ניתוח/פעולה עיקרית</Description> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </icd_type> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_gender = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                        <gender><Code>1</Code> <Description>זכר</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> <Code_btl>0</Code_btl></gender> 
                        <gender> <Code>2</Code> <Description>נקבה</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> <Code_btl>2</Code_btl> </gender> 
                        <gender> <Code>9</Code> <Description>לא ידוע</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> <Code_btl>1</Code_btl> </gender> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_id_type = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                        <id_type> <Code>4</Code> <Description>ת"ז פלסטינית</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </id_type> 
                        <id_type><Code>1</Code> <Description>תעודת זהות</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date><Update_date>1900-01-01T00:00:00+02:00</Update_date> </id_type> 
                        <id_type> <Code>2</Code> <Description>דרכון</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </id_type> 
                         <id_type><Code>3</Code> <Description>זמני</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </id_type>
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';

    public $dummy_response_institute_type = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                    <institute_type> <Code>3</Code> <Description>בית חולים לילדים</Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </institute_type> 
                    <institute_type><Code>58</Code> <Description>מרפאות אונקולוגיות בקהילה </Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>2017-01-18T15:20:22.097+02:00</Create_date> <Update_date>2017-01-18T15:20:22.097+02:00</Update_date> </institute_type> 
                    <institute_type> <Code>1</Code> <Description>אשפוז כללי      </Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </institute_type> 
                    <institute_type> <Code>2</Code> <Description>בתי יולדות      </Description> <From_date>1900-01-01T00:00:00+02:00</From_date> <Create_date>1900-01-01T00:00:00+02:00</Create_date> <Update_date>1900-01-01T00:00:00+02:00</Update_date> </institute_type> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_institute = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                            <institute> 
                            <Code>01101</Code> 
                            <Code_mbr>01101</Code_mbr> 
                            <Folder_num>0101000001</Folder_num> 
                            <Description>שיבא                </Description> 
                            <Description_long>מרכז רפואי ע"ש ד"ר ח. שיבא, תל-השומר    </Description_long> 
                            <Institute_type_code>1</Institute_type_code> 
                            <Ownership_code>1</Ownership_code> 
                            <City_code>8600</City_code> 
                            <Zip_code>52621</Zip_code> 
                            <Address_comments>תל-השומר  </Address_comments> 
                            <Email xml:space="preserve">   </Email> 
                            <Phone1>03-5303030 </Phone1> 
                            <Phone2>03-5302306 </Phone2> 
                            <Fax>03-5351508 </Fax> 
                            <Description_eng>CHAIM SHEBA MEDICAL CENTER    </Description_eng> 
                            <Address_eng>TEL HASHOMER 52621                      </Address_eng> 
                            <From_date>1900-01-01T00:00:00+02:00</From_date> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                            </institute> 
                            <institute> 
                            <Code>01102</Code> 
                            <Code_mbr>01102</Code_mbr> 
                            <Folder_num>0101000002</Folder_num> 
                            <Description>רמב"ם               </Description> 
                            <Description_long>מרכז רפואי ע"ש רמב"ם          </Description_long> 
                            <Institute_type_code>1</Institute_type_code> 
                            <Ownership_code>1</Ownership_code> 
                            <City_code>4000</City_code> 
                            <Zip_code>31096</Zip_code> 
                            <Address_comments>רח\' העליה 8, בת גלים ת.ד 2069           </Address_comments> 
                            <Email>REVACH@RAMBAM.HEALTH.GOV.IL    </Email> 
                            <Phone1>04-8542389 </Phone1> 
                            <Phone2>04-8542635 </Phone2> 
                            <Fax>04-8542907 </Fax> 
                            <Open_date>1986-01-01T00:00:00+02:00</Open_date> 
                            <Description_eng xml:space="preserve">       </Description_eng> 
                            <Address_eng>HHLIA 8\' BAT GALIM   </Address_eng> 
                            <From_date>1986-01-01T00:00:00+02:00</From_date> 
                            <Create_date>1986-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1986-01-01T00:00:00+02:00</Update_date> 
                            </institute> 
                            <institute> 
                            <Code>01103</Code> 
                            <Code_mbr>01103</Code_mbr> 
                            <Folder_num>0101000003</Folder_num> 
                            <Description>אסף הרופא           </Description> 
                            <Description_long>מרכז רפואי ע"ש אסף הרופא, צריפין  </Description_long> 
                            <Institute_type_code>1</Institute_type_code> 
                            <Ownership_code>1</Ownership_code> 
                            <City_code>2530</City_code> 
                            <Zip_code>70300</Zip_code> 
                            <Address_comments>צריפין      </Address_comments> 
                            <Email xml:space="preserve">                                        </Email> 
                            <Phone1>08-9648040 </Phone1> 
                            <Phone2>08-9779925 </Phone2> 
                            <Fax>08-9779502 </Fax> 
                            <Description_eng>ASSAF HAROFEH MEDICAL CENTER  </Description_eng> 
                            <Address_eng>ZERIFIN                                 </Address_eng> 
                            <From_date>1900-01-01T00:00:00+02:00</From_date> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                            </institute> 
                            <institute> 
                            <Code>01104</Code> 
                            <Code_mbr>01104</Code_mbr> 
                            <Folder_num>0101000004</Folder_num> 
                            <Description>וולפסון             </Description> 
                            <Description_long>מרכז רפואי ע"ש א. וולפסון     </Description_long> 
                            <Institute_type_code>1</Institute_type_code> 
                            <Ownership_code>1</Ownership_code> 
                            <City_code>6600</City_code> 
                            <Zip_code>58100</Zip_code> 
                            <Address_comments>רח\' הלוחמים 26       </Address_comments> 
                            <Email xml:space="preserve">     </Email> 
                            <Phone1>03-5028321 </Phone1> 
                            <Phone2>03-5028211 </Phone2> 
                            <Fax>03-5054482 </Fax> 
                            <Open_date>1980-01-01T00:00:00+02:00</Open_date> 
                            <Description_eng>WOOLFSON MEDICAL CENTER       </Description_eng> 
                            <Address_eng>HALOHAMIM 62\' 58100                     </Address_eng> 
                            <From_date>1980-01-01T00:00:00+02:00</From_date> 
                            <Create_date>1980-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1980-01-01T00:00:00+02:00</Update_date> 
                            </institute> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_language = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                         <language> 
                        <Code>1</Code> 
                        <Description>אוזבקית</Description> 
                        <From_date>2016-09-26T00:00:00+03:00</From_date> 
                        <Create_date>2016-09-26T08:46:28.933+03:00</Create_date> 
                        <Update_date>2016-09-26T08:46:28.933+03:00</Update_date> 
                      </language> 
                      <language> 
                        <Code>2</Code> 
                        <Description>אוקראינית</Description> 
                        <From_date>2016-09-26T00:00:00+03:00</From_date> 
                        <Create_date>2016-09-26T08:46:28.98+03:00</Create_date> 
                        <Update_date>2016-09-26T08:46:28.98+03:00</Update_date> 
                      </language> 
                      <language> 
                        <Code>3</Code> 
                        <Description>אורדו</Description> 
                        <From_date>2016-09-26T00:00:00+03:00</From_date> 
                        <Create_date>2016-09-26T08:46:29.027+03:00</Create_date> 
                        <Update_date>2016-09-26T08:46:29.027+03:00</Update_date> 
                      </language> 
                      <language> 
                        <Code>4</Code> 
                        <Description>אזרבייג\'נית</Description> 
                        <From_date>2016-09-26T00:00:00+03:00</From_date> 
                        <Create_date>2016-09-26T08:46:29.077+03:00</Create_date> 
                        <Update_date>2016-09-26T08:46:29.077+03:00</Update_date> 
                      </language> 
                      <language> 
                        <Code>5</Code> 
                        <Description>איטלקית</Description> 
                        <From_date>2016-09-26T00:00:00+03:00</From_date> 
                        <To_date>2016-09-26T08:46:29.12+03:00</To_date> 
                        <Update_date>2016-09-26T08:46:29.12+03:00</Update_date> 
                      </language> 
                    
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';

    public $dummy_response_ExtendedLogicalStatus = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                          <ExtendedLogicalStatus> 
    <Code>0</Code> 
    <Description>לא</Description> 
    <From_date>2016-07-12T00:00:00+03:00</From_date> 
    <Create_date>2016-07-12T18:04:21.05+03:00</Create_date> 
    <Update_date>2016-07-12T18:04:21.05+03:00</Update_date> 
  </ExtendedLogicalStatus> 
  <ExtendedLogicalStatus> 
    <Code>1</Code> 
    <Description>כן</Description> 
    <From_date>2016-07-12T00:00:00+03:00</From_date> 
    <Create_date>2016-07-12T18:04:32.25+03:00</Create_date> 
    <Update_date>2016-07-12T18:04:32.25+03:00</Update_date> 
  </ExtendedLogicalStatus> 
  <ExtendedLogicalStatus> 
    <Code>97</Code> 
    <Description>לא נקבע</Description> 
    <From_date>2016-07-12T00:00:00+03:00</From_date> 
    <Create_date>2016-07-12T18:04:54.713+03:00</Create_date> 
    <Update_date>2016-07-12T18:04:54.713+03:00</Update_date> 
  </ExtendedLogicalStatus> 
  <ExtendedLogicalStatus> 
    <Code>99</Code> 
    <Description>לא ידוע</Description> 
    <From_date>2016-07-12T00:00:00+03:00</From_date> 
    <Create_date>2016-07-12T18:05:07.357+03:00</Create_date> 
    <Update_date>2016-07-12T18:05:07.357+03:00</Update_date> 
  </ExtendedLogicalStatus> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_ICD9 = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                          <ICD9>
                            <Code>464</Code>
                            <Diag_or_proc>3</Diag_or_proc>
                            <Description>AC LARYNGITIS/TRACHEITIS</Description>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <To_date>2003-12-31T00:00:00+02:00</To_date>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>2004-01-01T00:00:00+02:00</Update_date>
                          </ICD9>
                          <ICD9>
                            <Code>464</Code>
                            <Diag_or_proc>4</Diag_or_proc>
                            <Description>INTEST STOMA REVISION</Description>
                            <From_date>1900-01-01T00:00:00+02:00</From_date>
                            <To_date>2003-12-31T00:00:00+02:00</To_date>
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date>
                            <Update_date>2004-01-01T00:00:00+02:00</Update_date>
                          </ICD9>
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';


    public $dummy_response_ICD10 = '<s:Envelope
	xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
	<s:Header>
		<ActivityId CorrelationId="8d6c69ce-4634-477e-a295-3eaf8d43fbdb"
			xmlns="http://schemas.microsoft.com/2004/09/ServiceModel/Diagnostics">425d6578-c526-4f14-b98a-43b3e4ecbaa6
		</ActivityId>
	</s:Header>
	<s:Body>
		<GenerationXMLDataTableResponse
			xmlns="http://www.health.gov.il">
			<GenerationXMLDataTableResult
				xmlns:a="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">
				<a:XMLData>
					<BroadCastTransDataSet>
                          <ICD10> 
                            <Code>Y502</Code> 
                            <Code2>Y50.2</Code2> 
                            <Description>Methylxanthines, not elsewhere classified</Description> 
                            <Description_long>Methylxanthines, not elsewhere classified</Description_long> 
                            <From_date>1900-01-01T00:00:00+02:00</From_date> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                          </ICD10> 
                          <ICD10> 
                            <Code>Y508</Code> 
                            <Code2>Y50.8</Code2> 
                            <Description>Other central nervous system stimulants</Description> 
                            <Description_long>Other central nervous system stimulants</Description_long> 
                            <From_date>1900-01-01T00:00:00+02:00</From_date> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                          </ICD10> 
                          <ICD10> 
                            <Code>Y509</Code> 
                            <Code2>Y50.9</Code2> 
                            <Description>Central nervous system stimulant, unspecified</Description> 
                            <Description_long>Central nervous system stimulant, unspecified</Description_long> 
                            <From_date>1900-01-01T00:00:00+02:00</From_date> 
                            <Create_date>1900-01-01T00:00:00+02:00</Create_date> 
                            <Update_date>1900-01-01T00:00:00+02:00</Update_date> 
                          </ICD10> 
					</BroadCastTransDataSet>
					</a:XMLData>
					<a:XMLschema>
						<xs:schema id="BroadCastTransDataSet"
							xmlns=""
							xmlns:xs="http://www.w3.org/2001/XMLSchema"
							xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
							<xs:element name="BroadCastTransDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
								<xs:complexType>
									<xs:choice minOccurs="0" maxOccurs="unbounded">
										<xs:element name="country">
											<xs:complexType>
												<xs:sequence>
													<xs:element name="country_code" type="xs:int" minOccurs="0" />
													<xs:element name="country_desc" type="xs:string" minOccurs="0" />
													<xs:element name="country_eng_desc" type="xs:string" minOccurs="0" />
													<xs:element name="continent_code" type="xs:short" minOccurs="0" />
													<xs:element name="from_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="to_date" type="xs:dateTime" minOccurs="0" />
													<xs:element name="country_merkava_code" type="xs:string" minOccurs="0" />
												</xs:sequence>
											</xs:complexType>
										</xs:element>
									</xs:choice>
								</xs:complexType>
							</xs:element>
						</xs:schema>
					</a:XMLschema>
				</GenerationXMLDataTableResult>
				<pResult>CompletSuccessfully</pResult>
			</GenerationXMLDataTableResponse>
		</s:Body>
	</s:Envelope>';
}