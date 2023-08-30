# Finna Code Sets

Support library for code sets used in Finna record formats.

- Provides interfaces and utility methods for working with code sets and related metadata models.
- Fetches code set values and other data from web APIs.
- Provides a pluggable caching mechanism for fetched and processed data.

The initial version provides support for [LRMI_FI](https://wiki.eduuni.fi/pages/viewpage.action?pageId=222560437) and the following sources:

- DVV Koodistot
    - [Educational levels](http://uri.suomi.fi/codelist/edtech/Koulutusaste)
    - [Licences](http://uri.suomi.fi/codelist/edtech/Licence)
- [OPH ePerusteet](https://wiki.eduuni.fi/display/OPHPALV/ePerusteet+julkiset+rajapinnat)
    - Educational levels
    - Educational subjects
    - Study contents
    - Study objectives
- [OPH Koodisto](https://wiki.eduuni.fi/display/OPHPALV/Koodistopalvelu)
  - Educational subjects
- [OPH Organisaatio](https://wiki.eduuni.fi/display/OPHPALV/Organisaatiopalvelu)
  - Organisations
