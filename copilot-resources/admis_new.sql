--
-- Struktura tabulky `actionsLogs`
--

CREATE TABLE `actionsLogs` (
  `idAction` int(11) NOT NULL,
  `idActionType` int(11) DEFAULT NULL,
  `idLocalProject` int(11) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `attributes`
--

CREATE TABLE `attributes` (
  `idObject` int(11) NOT NULL,
  `idAttributeType` int(11) NOT NULL,
  `value` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `calendarEvents`
--

CREATE TABLE `calendarEvents` (
  `idEvent` int(11) NOT NULL,
  `username` varchar(40) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `eventStart` datetime DEFAULT NULL,
  `eventEnd` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `deletedTimestamp` datetime DEFAULT NULL,
  `idOu` int(11) DEFAULT NULL,
  `deletedAuthor` varchar(40) DEFAULT NULL,
  `idEventUpdated` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `collaborator`
--

CREATE TABLE `collaborator` (
  `id` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `collaborator` varchar(40) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `begin` datetime DEFAULT NULL,
  `expiry` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `deadlines`
--

CREATE TABLE `deadlines` (
  `idProject` int(11) NOT NULL,
  `idDeadlineType` int(11) NOT NULL,
  `value` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `inserted` datetime DEFAULT current_timestamp(),
  `inserted_by` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `logins`
--

CREATE TABLE `logins` (
  `idLogin` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `loginTime` datetime NOT NULL,
  `ipAddress` varchar(16) NOT NULL,
  `result` text NOT NULL,
  `agent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `notifications`
--

CREATE TABLE `notifications` (
  `idNotification` int(11) NOT NULL,
  `username` varchar(40) DEFAULT NULL,
  `idAction` int(11) DEFAULT NULL,
  `viewed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `objects`
--

CREATE TABLE `objects` (
  `idObject` int(11) NOT NULL,
  `idProject` int(11) NOT NULL,
  `idObjectType` int(11) NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `objectType2Attribute`
--

CREATE TABLE `objectType2Attribute` (
  `idObjectType` int(11) NOT NULL,
  `idAttribute` int(11) NOT NULL,
  `idPhase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `ou`
--

CREATE TABLE `ou` (
  `idOu` int(11) NOT NULL,
  `parentOuId` int(11) DEFAULT NULL,
  `name` varchar(40) NOT NULL,
  `hidden` tinyint(1) DEFAULT NULL,
  `deletable` tinyint(1) DEFAULT NULL,
  `orderNum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `prices`
--

CREATE TABLE `prices` (
  `idPriceType` int(11) NOT NULL,
  `idProject` int(11) NOT NULL,
  `value` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `project2area`
--

CREATE TABLE `project2area` (
  `idProject` int(11) NOT NULL,
  `idArea` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `project2communication`
--

CREATE TABLE `project2communication` (
  `idProject2communication` int(11) NOT NULL,
  `idProject` int(11) NOT NULL,
  `idCommunication` int(11) NOT NULL,
  `stationingFrom` decimal(7,3) DEFAULT NULL,
  `stationingTo` decimal(7,3) DEFAULT NULL,
  `gpsN1` float(15,12) NOT NULL,
  `gpsN2` float(15,12) NOT NULL,
  `gpsE1` float(15,12) NOT NULL,
  `gpsE2` float(15,12) NOT NULL,
  `allPoints` text DEFAULT NULL,
  `comment` text DEFAULT NULL COMMENT 'Název cyklostezy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `project2company`
--

CREATE TABLE `project2company` (
  `idProject` int(11) NOT NULL,
  `idCompany` int(11) NOT NULL,
  `idCompanyType` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Struktura tabulky `project2contact`
--

CREATE TABLE `project2contact` (
  `idProject` int(11) NOT NULL,
  `idContact` int(11) NOT NULL,
  `idContactType` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `projectRelations`
--

CREATE TABLE `projectRelations` (
  `idRelation` int(11) NOT NULL,
  `username` varchar(40) DEFAULT NULL,
  `idProject` int(11) DEFAULT NULL,
  `idRelationType` int(11) DEFAULT NULL,
  `idProjectRelation` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `projects`
--

CREATE TABLE `projects` (
  `idProject` int(11) NOT NULL,
  `idProjectType` int(11) NOT NULL,
  `idProjectSubtype` int(11) DEFAULT NULL,
  `technologicalProjectType` enum('lite','normal','topic','') NOT NULL DEFAULT 'normal',
  `created` datetime NOT NULL,
  `name` varchar(300) NOT NULL,
  `subject` text NOT NULL,
  `editor` varchar(40) NOT NULL,
  `author` varchar(40) NOT NULL,
  `idFinSource` int(11) DEFAULT NULL,
  `idFinSourcePD` int(11) DEFAULT NULL,
  `idPhase` int(11) NOT NULL,
  `idLocalProject` int(11) NOT NULL,
  `ginisOrAthena` varchar(1) DEFAULT NULL,
  `noteGinisOrAthena` varchar(100) DEFAULT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deleteAuthor` varchar(40) DEFAULT NULL,
  `inConcept` tinyint(1) DEFAULT NULL,
  `dateEvidence` tinyint(1) DEFAULT NULL,
  `deadlineDurUrRequired` tinyint(1) DEFAULT NULL COMMENT 'Are deadlines dur ur required?',
  `deadlineEIARequired` tinyint(1) DEFAULT NULL COMMENT 'is deadline eia required?',
  `deadlineStudyRequired` tinyint(1) DEFAULT NULL COMMENT 'is deadline study required?',
  `deadlineTesRequired` tinyint(1) DEFAULT NULL,
  `mergedDeadlines` smallint(6) DEFAULT NULL,
  `constructionTime` int(11) DEFAULT NULL,
  `constructionTimeWeeksOrMonths` enum('w','m','d') DEFAULT NULL,
  `mergePricePDAD` tinyint(1) NOT NULL DEFAULT 0,
  `constructionWarrantyPeriod` int(11) DEFAULT NULL,
  `technologyWarrantyPeriod` int(11) DEFAULT NULL,
  `priorityAtts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `passable` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Struktura tabulky `projects2documents`
--

CREATE TABLE `projects2documents` (
  `idDocumentLocal` int(11) NOT NULL,
  `idDocument` varchar(30) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `size` float DEFAULT NULL COMMENT 'MB ',
  `path` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `deletedAt` datetime DEFAULT NULL,
  `restored` tinyint(1) DEFAULT NULL,
  `restoredFrom` int(11) DEFAULT NULL,
  `documentAuthor` varchar(40) DEFAULT NULL,
  `idProject` int(11) DEFAULT NULL,
  `idDocumentCategory` int(11) DEFAULT NULL,
  `idDocType` int(11) NOT NULL,
  `deletedAuthor` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Zástupná struktura pro pohled `projectsPrioritiesParsed`
-- (Vlastní pohled viz níže)
--
CREATE TABLE `projectsPrioritiesParsed` (
`idProject` int(11)
,`dopravni_zatizeni` longtext
,`spolufinancovani` longtext
,`dopravni_vyznam` longtext
,`technicky_stav` longtext
,`stavebni_stav` longtext
,`zivotni_prostred` longtext
,`regionalni_vyznam` longtext
,`jedina_pristupova_cesta` longtext
,`stav_pripravy` longtext
,`hromadna_doprava` longtext
,`nehodova_lokalita` longtext
,`idProjectType` int(11)
,`idProjectSubtype` int(11)
);

-- --------------------------------------------------------

--
-- Zástupná struktura pro pohled `projectsPriorityScore`
-- (Vlastní pohled viz níže)
--
CREATE TABLE `projectsPriorityScore` (
`idProject` int(11)
,`priorityScore` double
,`maxScore` double
,`correctionValue` double
);

-- --------------------------------------------------------

--
-- Struktura tabulky `projectsToPublish`
--

CREATE TABLE `projectsToPublish` (
  `idProject` int(11) NOT NULL,
  `publishAt` datetime NOT NULL,
  `publishBy` varchar(60) NOT NULL,
  `deletedAt` datetime DEFAULT NULL,
  `deletedBy` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `projectSubtypes2ObjectTypes`
--

CREATE TABLE `projectSubtypes2ObjectTypes` (
  `idProjectSubtypes2ObjectTypes` int(11) NOT NULL,
  `idProjectSubtype` int(11) NOT NULL,
  `idObjectType` int(11) NOT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `projectVersions`
--

CREATE TABLE `projectVersions` (
  `idLocalProject` int(11) NOT NULL,
  `idPhase` int(11) DEFAULT NULL,
  `assignments` text DEFAULT NULL,
  `idProject` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `validTo` datetime DEFAULT NULL,
  `historyDump` text NOT NULL,
  `author` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeActionTypes`
--

CREATE TABLE `rangeActionTypes` (
  `idActionType` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `description` text DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeAreas`
--

CREATE TABLE `rangeAreas` (
  `idArea` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeAttributesGroups`
--

CREATE TABLE `rangeAttributesGroups` (
  `idAttGroup` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeAttributeTypes`
--

CREATE TABLE `rangeAttributeTypes` (
  `idAttributeType` int(11) NOT NULL,
  `idAttGroup` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `type` varchar(40) NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeAuthoritiyTypes`
--

CREATE TABLE `rangeAuthoritiyTypes` (
  `idAuthorityType` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hidden` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeCommunications`
--

CREATE TABLE `rangeCommunications` (
  `idCommunication` int(11) NOT NULL,
  `idCommunicationType` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeCommunicationTypes`
--

CREATE TABLE `rangeCommunicationTypes` (
  `idCommunicationType` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeCompanies`
--

CREATE TABLE `rangeCompanies` (
  `idCompany` int(11) NOT NULL,
  `name` varchar(180) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `ic` int(11) DEFAULT NULL,
  `dic` varchar(50) DEFAULT NULL,
  `www` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeCompanyTypes`
--

CREATE TABLE `rangeCompanyTypes` (
  `idCompanyType` int(11) NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeContacts`
--

CREATE TABLE `rangeContacts` (
  `idContact` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime DEFAULT NULL,
  `updated_by` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeContactTypes`
--

CREATE TABLE `rangeContactTypes` (
  `idContactType` int(11) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `nameEn` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `hidden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeDeadlineTypes`
--

CREATE TABLE `rangeDeadlineTypes` (
  `idDeadlineType` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `nameEn` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `availableInPhase` int(11) DEFAULT NULL COMMENT 'Omezuje selecty pro vyber terminu, id odpovida idPhase ,které je stejné jako idphase aktuálního stavu projektu. tzn Záměr -> V přípravě dávám id záměru',
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeDocumentCategories`
--

CREATE TABLE `rangeDocumentCategories` (
  `idDocumentCategory` int(11) NOT NULL COMMENT 'NEPRECISLOVAVAT ID, v KODU JSOU NA NE ODKAZY',
  `name` varchar(40) NOT NULL,
  `description` varchar(40) NOT NULL,
  `orderNum` int(11) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeDocumentMimeTypes`
--

CREATE TABLE `rangeDocumentMimeTypes` (
  `extension` varchar(7) NOT NULL,
  `description` varchar(43) DEFAULT NULL,
  `mime` varchar(73) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeDocumentTypes`
--

CREATE TABLE `rangeDocumentTypes` (
  `idDocType` int(11) NOT NULL,
  `name` varchar(90) NOT NULL,
  `description` text DEFAULT NULL,
  `extension` varchar(40) NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeFinancialSources`
--

CREATE TABLE `rangeFinancialSources` (
  `idFinSource` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeObjectTypes`
--

CREATE TABLE `rangeObjectTypes` (
  `idObjectType` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangePhases`
--

CREATE TABLE `rangePhases` (
  `idPhase` int(11) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `nameEn` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `level` int(11) DEFAULT NULL,
  `phasing` tinyint(1) NOT NULL DEFAULT 0,
  `phaseColor` varchar(40) DEFAULT NULL,
  `phaseColorClass` varchar(40) DEFAULT NULL,
  `hidden` int(11) NOT NULL,
  `phaseForLiteProject` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangePriceSubtype`
--

CREATE TABLE `rangePriceSubtype` (
  `idPriceSubtypes` int(5) NOT NULL,
  `name` varchar(40) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangePriceTypes`
--

CREATE TABLE `rangePriceTypes` (
  `idPriceType` int(11) NOT NULL,
  `name` varchar(80) DEFAULT NULL,
  `nameEn` varchar(40) NOT NULL,
  `hidden` int(11) NOT NULL,
  `idPriceSubtype` int(11) NOT NULL,
  `ordering` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangePriorityScaleConfig`
--

CREATE TABLE `rangePriorityScaleConfig` (
  `idPriorityConfig` int(11) NOT NULL,
  `configJson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Zástupná struktura pro pohled `rangePriorityScaleConfigParsed`
-- (Vlastní pohled viz níže)
--
CREATE TABLE `rangePriorityScaleConfigParsed` (
`idProjectType` int(11)
,`idProjectSubtype` int(11)
,`dopravni_zatizeni` longtext
,`spolufinancovani` longtext
,`dopravni_vyznam` longtext
,`technicky_stav` longtext
,`stavebni_stav` longtext
,`zivotni_prostred` longtext
,`regionalni_vyznam` longtext
,`jedina_pristupova_cesta` longtext
,`stav_pripravy` longtext
,`hromadna_doprava` longtext
,`nehodova_lokalita` longtext
);

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeProjectSubtypes`
--

CREATE TABLE `rangeProjectSubtypes` (
  `idProjectSubtype` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeProjectTypes`
--

CREATE TABLE `rangeProjectTypes` (
  `idProjectType` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `hidden` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeRelationTypes`
--

CREATE TABLE `rangeRelationTypes` (
  `idRelationType` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `description` text DEFAULT NULL,
  `relationFromProjectRelation` int(11) NOT NULL COMMENT 'Opacna relace od projektu v relaci',
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeRoleTypes`
--

CREATE TABLE `rangeRoleTypes` (
  `idRoleType` int(11) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeSuspensionReasons`
--

CREATE TABLE `rangeSuspensionReasons` (
  `idSuspensionReason` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeSuspensionSources`
--

CREATE TABLE `rangeSuspensionSources` (
  `idSuspensionSource` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeTags`
--

CREATE TABLE `rangeTags` (
  `idTag` int(11) NOT NULL,
  `tagName` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `author` varchar(40) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `popularity` int(11) NOT NULL DEFAULT 1,
  `tagColor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeTaskStatuses`
--

CREATE TABLE `rangeTaskStatuses` (
  `idTaskStatus` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `isTerminal` tinyint(1) NOT NULL,
  `isEnabled` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `statusColor` text NOT NULL,
  `statusClass` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeWarranties`
--

CREATE TABLE `rangeWarranties` (
  `idWarranty` int(11) NOT NULL,
  `period` int(11) NOT NULL COMMENT 'months',
  `idWarrantyType` int(11) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `rangeWarrantiesTypes`
--

CREATE TABLE `rangeWarrantiesTypes` (
  `idWarrantyType` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `hidden` int(11) NOT NULL,
  `nameForPOST` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Struktura tabulky `reportConfig`
--

CREATE TABLE `reportConfig` (
  `idReportConfig` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `ouIds` text DEFAULT NULL COMMENT 'V defaultu editor report pošle',
  `usernames` text DEFAULT NULL,
  `reportType` enum('editor','manager','noreport','dummy') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `reportsHistory`
--

CREATE TABLE `reportsHistory` (
  `idReportHistory` int(11) NOT NULL,
  `pricesPerPhases` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `projectsPerPhases` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `projectsEditorsBreakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created` datetime NOT NULL,
  `relatedToConfig` varchar(60) NOT NULL,
  `relatedToUsername` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `suspensions`
--

CREATE TABLE `suspensions` (
  `idSuspension` int(11) NOT NULL,
  `idProject` int(11) DEFAULT NULL,
  `idSuspensionSource` int(11) DEFAULT NULL,
  `idSuspensionReason` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `dateFrom` date DEFAULT NULL,
  `dateTo` date DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `username` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `tags2documents`
--

CREATE TABLE `tags2documents` (
  `idTag` int(11) NOT NULL,
  `idDocument` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `taskReactions`
--

CREATE TABLE `taskReactions` (
  `idTask` int(11) NOT NULL,
  `reaction` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `createdBy` varchar(60) NOT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedBy` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `tasksProject`
--

CREATE TABLE `tasksProject` (
  `idTask` int(11) NOT NULL,
  `createdBy` varchar(60) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `deletedBy` varchar(80) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `relatedToProjectId` int(11) NOT NULL,
  `privateTask` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Struktura tabulky `taskVersions`
--

CREATE TABLE `taskVersions` (
  `idTask` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` text DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `createdBy` varchar(60) NOT NULL,
  `idTaskStatus` int(11) NOT NULL,
  `deadlineTo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `type2subtype`
--

CREATE TABLE `type2subtype` (
  `idProjectType` int(11) NOT NULL,
  `idProjectSubtype` int(11) NOT NULL,
  `idPriorityConfig` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `username` varchar(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `idOu` int(11) NOT NULL,
  `idRoleType` int(11) NOT NULL,
  `idAuthorityType` int(11) NOT NULL,
  `email` varchar(40) NOT NULL,
  `updated` datetime DEFAULT NULL,
  `password` text NOT NULL,
  `accessDenied` tinyint(1) NOT NULL,
  `idReportConfig` int(11) DEFAULT 3 COMMENT 'slouzi pro managerske reporty',
  `editorReport` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 zapina editorsky report',
  `created` datetime NOT NULL,
  `created_by` varchar(60) NOT NULL,
  `updated_by` varchar(60) NOT NULL,
  `deleted` timestamp NULL DEFAULT NULL,
  `deleted_by` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

-- --------------------------------------------------------

--
-- Zástupná struktura pro pohled `viewActionsLogAll`
-- (Vlastní pohled viz níže)
--
CREATE TABLE `viewActionsLogAll` (
`actionName` varchar(60)
,`idAction` int(11)
,`idActionType` int(11)
,`idLocalProject` int(11)
,`username` varchar(40)
,`created` datetime
,`projectName` varchar(300)
,`idProject` int(11)
,`idPhase` int(11)
,`phaseName` varchar(40)
,`ouName` varchar(40)
,`nameUser` varchar(80)
);

-- --------------------------------------------------------

--
-- Zástupná struktura pro pohled `viewActionsLogNoHidden`
-- (Vlastní pohled viz níže)
--
CREATE TABLE `viewActionsLogNoHidden` (
`actionName` varchar(60)
,`idAction` int(11)
,`idActionType` int(11)
,`idLocalProject` int(11)
,`username` varchar(40)
,`created` datetime
,`projectName` varchar(300)
,`idProject` int(11)
,`idPhase` int(11)
,`phaseName` varchar(40)
,`ouName` varchar(40)
,`nameUser` varchar(80)
);

-- --------------------------------------------------------

--
-- Zástupná struktura pro pohled `viewDocumentsActualVersions`
-- (Vlastní pohled viz níže)
--
CREATE TABLE `viewDocumentsActualVersions` (
`idDocument` varchar(30)
,`idDocumentLocal` int(11)
,`idDocumentCategory` int(11)
,`name` varchar(50)
,`categoryName` varchar(40)
,`path` varchar(100)
,`description` text
,`idDocType` int(11)
,`documentTypeName` varchar(40)
,`created` datetime
,`documentAuthor` varchar(40)
,`IdProject` int(11)
,`version` int(11)
,`restored` tinyint(1)
,`restoredFrom` int(11)
,`size` float
);

-- --------------------------------------------------------

--
-- Zástupná struktura pro pohled `viewProjectsActive`
-- (Vlastní pohled viz níže)
--
CREATE TABLE `viewProjectsActive` (
`idProject` int(11)
,`idLocalProject` int(11)
,`idProjectType` int(11)
,`mergePricePDAD` tinyint(1)
,`projectTypeName` varchar(40)
,`idProjectSubtype` int(11)
,`projectSubtypeName` varchar(40)
,`created` datetime
,`updated` datetime
,`name` text
,`passable` tinyint(1)
,`subject` mediumtext
,`editor` varchar(40)
,`editorName` varchar(80)
,`author` varchar(40)
,`idFinSource` int(11)
,`idPhase` int(11)
,`ginisOrAthena` varchar(1)
,`noteGinisOrAthena` varchar(100)
,`inConcept` tinyint(1)
,`dateEvidence` tinyint(1)
,`priorityScore` double
,`idOu` int(11)
,`ouName` varchar(40)
,`phaseName` varchar(40)
);

-- --------------------------------------------------------

--
-- Zástupná struktura pro pohled `viewProjectsWithJoinsActive`
-- (Vlastní pohled viz níže)
--
CREATE TABLE `viewProjectsWithJoinsActive` (
`idProject` int(11)
,`etapaRodic` int(11)
,`mergePricePDAD` tinyint(1)
,`idProjectType` int(11)
,`idProjectSubtype` int(11)
,`created` datetime
,`name` varchar(300)
,`idOu` int(11)
,`subject` text
,`editor` varchar(40)
,`author` varchar(40)
,`idFinSource` int(11)
,`disableEtapa` int(1)
,`idPhase` int(11)
,`idLocalProject` int(11)
,`ginisOrAthena` varchar(1)
,`noteGinisOrAthena` varchar(100)
,`deletedDate` datetime
,`deleteAuthor` varchar(40)
,`idArea` int(11)
,`inConcept` tinyint(1)
,`dateEvidence` tinyint(1)
,`idCommunication` int(11)
,`stationingFrom` decimal(7,3)
,`stationingTo` decimal(7,3)
,`gpsN1` float(15,12)
,`gpsN2` float(15,12)
,`gpsE1` float(15,12)
,`gpsE2` float(15,12)
,`supervisorCompanyId` int(11)
,`buildCompanyId` int(11)
,`projectCompanyId` int(11)
,`published` int(1)
,`priorityScore` double
,`correctionValue` double
,`suspended` int(1)
,`phaseName` varchar(40)
,`constructionWarranty` datetime
,`technologyWarranty` datetime
);

-- --------------------------------------------------------

--
-- Struktura pro pohled `projectsPrioritiesParsed`
--
DROP TABLE IF EXISTS `projectsPrioritiesParsed`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `projectsPrioritiesParsed`  AS SELECT `projects`.`idProject` AS `idProject`, json_value(`projects`.`priorityAtts`,'$.dopravni_zatizeni') AS `dopravni_zatizeni`, json_value(`projects`.`priorityAtts`,'$.spolufinancovani') AS `spolufinancovani`, json_value(`projects`.`priorityAtts`,'$.dopravni_vyznam') AS `dopravni_vyznam`, json_value(`projects`.`priorityAtts`,'$.technicky_stav') AS `technicky_stav`, json_value(`projects`.`priorityAtts`,'$.stavebni_stav') AS `stavebni_stav`, json_value(`projects`.`priorityAtts`,'$.zivotni_prostred') AS `zivotni_prostred`, json_value(`projects`.`priorityAtts`,'$.regionalni_vyznam') AS `regionalni_vyznam`, json_value(`projects`.`priorityAtts`,'$.jedina_pristupova_cesta') AS `jedina_pristupova_cesta`, json_value(`projects`.`priorityAtts`,'$.stav_pripravy') AS `stav_pripravy`, json_value(`projects`.`priorityAtts`,'$.hromadna_doprava') AS `hromadna_doprava`, json_value(`projects`.`priorityAtts`,'$.nehodova_lokalita') AS `nehodova_lokalita`, `projects`.`idProjectType` AS `idProjectType`, `projects`.`idProjectSubtype` AS `idProjectSubtype` FROM `projects` WHERE `projects`.`priorityAtts` is not null ;

-- --------------------------------------------------------

--
-- Struktura pro pohled `projectsPriorityScore`
--
DROP TABLE IF EXISTS `projectsPriorityScore`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `projectsPriorityScore`  AS SELECT `tmp`.`idProject` AS `idProject`, `tmp`.`priorityScore` AS `priorityScore`, `tmp`.`maxScore` AS `maxScore`, `tmp`.`priorityScore`/ `tmp`.`maxScore` AS `correctionValue` FROM (select `pp`.`idProject` AS `idProject`,(`pp`.`dopravni_zatizeni` * `rp`.`dopravni_zatizeni` + `pp`.`spolufinancovani` * `rp`.`spolufinancovani` + `pp`.`dopravni_vyznam` * `rp`.`dopravni_vyznam` + `pp`.`technicky_stav` * `rp`.`technicky_stav` + `rp`.`stavebni_stav` * `pp`.`stavebni_stav` + `pp`.`zivotni_prostred` * `rp`.`zivotni_prostred` + `pp`.`regionalni_vyznam` * `rp`.`regionalni_vyznam` + `pp`.`jedina_pristupova_cesta` * `rp`.`jedina_pristupova_cesta` + `pp`.`stav_pripravy` * `rp`.`stav_pripravy` + `pp`.`hromadna_doprava` * `rp`.`hromadna_doprava` + `pp`.`nehodova_lokalita` * `rp`.`nehodova_lokalita`) / 10 AS `priorityScore`,(10 * `rp`.`dopravni_zatizeni` + 10 * `rp`.`spolufinancovani` + 10 * `rp`.`dopravni_vyznam` + 10 * `rp`.`technicky_stav` + `rp`.`stavebni_stav` * 10 + 10 * `rp`.`zivotni_prostred` + 10 * `rp`.`regionalni_vyznam` + 10 * `rp`.`jedina_pristupova_cesta` + 10 * `rp`.`stav_pripravy` + 10 * `rp`.`hromadna_doprava` + 10 * `rp`.`nehodova_lokalita`) / 10 AS `maxScore` from (`projectsPrioritiesParsed` `pp` join `rangePriorityScaleConfigParsed` `rp` on(`pp`.`idProjectType` = `rp`.`idProjectType` and `pp`.`idProjectSubtype` = `rp`.`idProjectSubtype`))) AS `tmp` ;

-- --------------------------------------------------------

--
-- Struktura pro pohled `rangePriorityScaleConfigParsed`
--
DROP TABLE IF EXISTS `rangePriorityScaleConfigParsed`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `rangePriorityScaleConfigParsed`  AS SELECT `type2subtype`.`idProjectType` AS `idProjectType`, `type2subtype`.`idProjectSubtype` AS `idProjectSubtype`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.dopravni_zatizeni') AS `dopravni_zatizeni`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.spolufinancovani') AS `spolufinancovani`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.dopravni_vyznam') AS `dopravni_vyznam`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.technicky_stav') AS `technicky_stav`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.stavebni_stav') AS `stavebni_stav`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.zivotni_prostred') AS `zivotni_prostred`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.regionalni_vyznam') AS `regionalni_vyznam`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.jedina_pristupova_cesta') AS `jedina_pristupova_cesta`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.stav_pripravy') AS `stav_pripravy`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.hromadna_doprava') AS `hromadna_doprava`, json_value(`rangePriorityScaleConfig`.`configJson`,'$.nehodova_lokalita') AS `nehodova_lokalita` FROM (`rangePriorityScaleConfig` join `type2subtype` on(`rangePriorityScaleConfig`.`idPriorityConfig` = `type2subtype`.`idPriorityConfig`)) ;

-- --------------------------------------------------------

--
-- Struktura pro pohled `viewActionsLogAll`
--
DROP TABLE IF EXISTS `viewActionsLogAll`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewActionsLogAll`  AS SELECT `rangeActionTypes`.`name` AS `actionName`, `actionsLogs`.`idAction` AS `idAction`, `actionsLogs`.`idActionType` AS `idActionType`, `actionsLogs`.`idLocalProject` AS `idLocalProject`, `actionsLogs`.`username` AS `username`, `actionsLogs`.`created` AS `created`, `projects`.`name` AS `projectName`, `projects`.`idProject` AS `idProject`, `projects`.`idPhase` AS `idPhase`, `rangePhases`.`name` AS `phaseName`, `ou`.`name` AS `ouName`, `users`.`name` AS `nameUser` FROM ((((((`actionsLogs` join `projectVersions` on(`actionsLogs`.`idLocalProject` = `projectVersions`.`idLocalProject`)) join `projects` on(`projectVersions`.`idProject` = `projects`.`idProject`)) join `users` on(`actionsLogs`.`username` = `users`.`username`)) join `rangePhases` on(`projects`.`idPhase` = `rangePhases`.`idPhase`)) join `ou` on(`users`.`idOu` = `ou`.`idOu`)) join `rangeActionTypes` on(`actionsLogs`.`idActionType` = `rangeActionTypes`.`idActionType`)) ;

-- --------------------------------------------------------

--
-- Struktura pro pohled `viewActionsLogNoHidden`
--
DROP TABLE IF EXISTS `viewActionsLogNoHidden`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewActionsLogNoHidden`  AS SELECT `rangeActionTypes`.`name` AS `actionName`, `actionsLogs`.`idAction` AS `idAction`, `actionsLogs`.`idActionType` AS `idActionType`, `actionsLogs`.`idLocalProject` AS `idLocalProject`, `actionsLogs`.`username` AS `username`, `actionsLogs`.`created` AS `created`, `projects`.`name` AS `projectName`, `projects`.`idProject` AS `idProject`, `projects`.`idPhase` AS `idPhase`, `rangePhases`.`name` AS `phaseName`, `ou`.`name` AS `ouName`, `users`.`name` AS `nameUser` FROM ((((((`actionsLogs` join `projectVersions` on(`actionsLogs`.`idLocalProject` = `projectVersions`.`idLocalProject`)) join `projects` on(`projectVersions`.`idProject` = `projects`.`idProject`)) join `users` on(`actionsLogs`.`username` = `users`.`username`)) join `rangePhases` on(`projects`.`idPhase` = `rangePhases`.`idPhase`)) join `ou` on(`users`.`idOu` = `ou`.`idOu`)) join `rangeActionTypes` on(`actionsLogs`.`idActionType` = `rangeActionTypes`.`idActionType`)) WHERE `rangeActionTypes`.`hidden` is not true ;

-- --------------------------------------------------------

--
-- Struktura pro pohled `viewDocumentsActualVersions`
--
DROP TABLE IF EXISTS `viewDocumentsActualVersions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewDocumentsActualVersions`  AS SELECT `yt`.`idDocument` AS `idDocument`, `yt`.`idDocumentLocal` AS `idDocumentLocal`, `yt`.`idDocumentCategory` AS `idDocumentCategory`, `yt`.`name` AS `name`, `rangeDocumentCategories`.`name` AS `categoryName`, `yt`.`path` AS `path`, `yt`.`description` AS `description`, `yt`.`idDocType` AS `idDocType`, `rangeDocumentTypes`.`extension` AS `documentTypeName`, `yt`.`created` AS `created`, `yt`.`documentAuthor` AS `documentAuthor`, `yt`.`idProject` AS `IdProject`, `yt`.`version` AS `version`, `yt`.`restored` AS `restored`, `yt`.`restoredFrom` AS `restoredFrom`, `yt`.`size` AS `size` FROM ((`projects2documents` `yt` join `rangeDocumentCategories` on(`yt`.`idDocumentCategory` = `rangeDocumentCategories`.`idDocumentCategory`)) join `rangeDocumentTypes` on(`yt`.`idDocType` = `rangeDocumentTypes`.`idDocType`)) WHERE `yt`.`deleted` is not true AND `yt`.`version` = (select max(`st`.`version`) from `projects2documents` `st` where `yt`.`idDocument` = `st`.`idDocument`) ;

-- --------------------------------------------------------

--
-- Struktura pro pohled `viewProjectsActive`
--
DROP TABLE IF EXISTS `viewProjectsActive`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewProjectsActive`  AS SELECT `tt`.`idProject` AS `idProject`, `tt`.`idLocalProject` AS `idLocalProject`, `tt`.`idProjectType` AS `idProjectType`, `tt`.`mergePricePDAD` AS `mergePricePDAD`, `tt`.`projectTypeName` AS `projectTypeName`, `tt`.`idProjectSubtype` AS `idProjectSubtype`, `tt`.`projectSubtypeName` AS `projectSubtypeName`, `tt`.`created` AS `created`, `tt`.`updated` AS `updated`, `tt`.`name` AS `name`, `tt`.`passable` AS `passable`, `tt`.`subject` AS `subject`, `tt`.`editor` AS `editor`, `tt`.`editorName` AS `editorName`, `tt`.`author` AS `author`, `tt`.`idFinSource` AS `idFinSource`, `tt`.`idPhase` AS `idPhase`, `tt`.`ginisOrAthena` AS `ginisOrAthena`, `tt`.`noteGinisOrAthena` AS `noteGinisOrAthena`, `tt`.`inConcept` AS `inConcept`, `tt`.`dateEvidence` AS `dateEvidence`, `tt`.`priorityScore` AS `priorityScore`, `tt`.`idOu` AS `idOu`, `tt`.`ouName` AS `ouName`, `rangePhases`.`name` AS `phaseName` FROM ((select `p`.`idProject` AS `idProject`,`p`.`idLocalProject` AS `idLocalProject`,`p`.`idProjectType` AS `idProjectType`,`p`.`mergePricePDAD` AS `mergePricePDAD`,`rpt`.`name` AS `projectTypeName`,`p`.`idProjectSubtype` AS `idProjectSubtype`,`rps`.`name` AS `projectSubtypeName`,`p`.`created` AS `created`,`pv`.`created` AS `updated`,`p`.`passable` AS `passable`,concat(ucase(left(`p`.`name`,1)),substr(`p`.`name`,2)) AS `name`,concat(ucase(left(`p`.`subject`,1)),substr(`p`.`subject`,2)) AS `subject`,`p`.`editor` AS `editor`,`u`.`name` AS `editorName`,`p`.`author` AS `author`,`p`.`idFinSource` AS `idFinSource`,case when exists(select `projectRelations`.`idProjectRelation` from `projectRelations` where `projectRelations`.`idProject` = `p`.`idProject` and `projectRelations`.`idRelationType` = 3 limit 1) then (select max(`p1`.`idPhase`) from `projects` `p1` where `p1`.`idProject` in (select `projectRelations`.`idProjectRelation` from `projectRelations` where `p1`.`deletedDate` is null and `projectRelations`.`idProject` = `p`.`idProject` and `projectRelations`.`idRelationType` = 3)) else `p`.`idPhase` end AS `idPhase`,`p`.`ginisOrAthena` AS `ginisOrAthena`,`p`.`noteGinisOrAthena` AS `noteGinisOrAthena`,`p`.`inConcept` AS `inConcept`,`p`.`dateEvidence` AS `dateEvidence`,`projectsPriorityScore`.`priorityScore` AS `priorityScore`,`u`.`idOu` AS `idOu`,`ou`.`name` AS `ouName` from ((((((`projects` `p` left join `projectVersions` `pv` on(`p`.`idProject` = `pv`.`idProject` and `pv`.`idLocalProject` = `p`.`idLocalProject`)) left join `rangeProjectTypes` `rpt` on(`p`.`idProjectType` = `rpt`.`idProjectType`)) left join `projectsPriorityScore` on(`p`.`idProject` = `projectsPriorityScore`.`idProject`)) left join `rangeProjectSubtypes` `rps` on(`p`.`idProjectSubtype` = `rps`.`idProjectSubtype`)) left join `users` `u` on(`u`.`username` = `p`.`editor`)) left join `ou` on(`u`.`idOu` = `ou`.`idOu`)) where `p`.`deletedDate` is null and `p`.`deleteAuthor` is null) `tt` join `rangePhases` on(`tt`.`idPhase` = `rangePhases`.`idPhase`)) ;

-- --------------------------------------------------------

--
-- Struktura pro pohled `viewProjectsWithJoinsActive`
--
DROP TABLE IF EXISTS `viewProjectsWithJoinsActive`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewProjectsWithJoinsActive`  AS SELECT `pr`.`idProject` AS `idProject`, `pr`.`etapaRodic` AS `etapaRodic`, `pr`.`mergePricePDAD` AS `mergePricePDAD`, `pr`.`idProjectType` AS `idProjectType`, `pr`.`idProjectSubtype` AS `idProjectSubtype`, `pr`.`created` AS `created`, `pr`.`name` AS `name`, `pr`.`idOu` AS `idOu`, `pr`.`subject` AS `subject`, `pr`.`editor` AS `editor`, `pr`.`author` AS `author`, `pr`.`idFinSource` AS `idFinSource`, `pr`.`disableEtapa` AS `disableEtapa`, `pr`.`idPhase` AS `idPhase`, `pr`.`idLocalProject` AS `idLocalProject`, `pr`.`ginisOrAthena` AS `ginisOrAthena`, `pr`.`noteGinisOrAthena` AS `noteGinisOrAthena`, `pr`.`deletedDate` AS `deletedDate`, `pr`.`deleteAuthor` AS `deleteAuthor`, `pr`.`idArea` AS `idArea`, `pr`.`inConcept` AS `inConcept`, `pr`.`dateEvidence` AS `dateEvidence`, `pr`.`idCommunication` AS `idCommunication`, `pr`.`stationingFrom` AS `stationingFrom`, `pr`.`stationingTo` AS `stationingTo`, `pr`.`gpsN1` AS `gpsN1`, `pr`.`gpsN2` AS `gpsN2`, `pr`.`gpsE1` AS `gpsE1`, `pr`.`gpsE2` AS `gpsE2`, `pr`.`supervisorCompanyId` AS `supervisorCompanyId`, `pr`.`buildCompanyId` AS `buildCompanyId`, `pr`.`projectCompanyId` AS `projectCompanyId`, `pr`.`published` AS `published`, `pr`.`priorityScore` AS `priorityScore`, `pr`.`correctionValue` AS `correctionValue`, `pr`.`suspended` AS `suspended`, `rp`.`name` AS `phaseName`, `pr`.`constructionWarranty` AS `constructionWarranty`, `pr`.`technologyWarranty` AS `technologyWarranty` FROM ((select `projects`.`idProject` AS `idProject`,case when exists(select `pr`.`idProjectRelation` from `projectRelations` `pr` where `pr`.`idRelationType` = 2 and `pr`.`idProject` = `projects`.`idProject` limit 1) then (select `pr`.`idProjectRelation` from `projectRelations` `pr` where `pr`.`idRelationType` = 2 and `pr`.`idProject` = `projects`.`idProject` limit 1) else NULL end AS `etapaRodic`,`projects`.`mergePricePDAD` AS `mergePricePDAD`,`projects`.`idProjectType` AS `idProjectType`,`projects`.`idProjectSubtype` AS `idProjectSubtype`,`projects`.`created` AS `created`,`projects`.`name` AS `name`,`projects`.`subject` AS `subject`,`projects`.`editor` AS `editor`,`projects`.`author` AS `author`,`projects`.`idFinSource` AS `idFinSource`,case when exists(select `projectRelations`.`idProjectRelation` from `projectRelations` where `projectRelations`.`idProject` = `projects`.`idProject` and `projectRelations`.`idRelationType` = 3 limit 1) then 1 else 0 end AS `disableEtapa`,case when exists(select `projectRelations`.`idProjectRelation` from `projectRelations` where `projectRelations`.`idProject` = `projects`.`idProject` and `projectRelations`.`idRelationType` = 3 limit 1) then (select max(`p1`.`idPhase`) from `projects` `p1` where `p1`.`idProject` in (select `projectRelations`.`idProjectRelation` from `projectRelations` where `p1`.`deletedDate` is null and `projectRelations`.`idProject` = `projects`.`idProject` and `projectRelations`.`idRelationType` = 3)) else `projects`.`idPhase` end AS `idPhase`,`projects`.`idLocalProject` AS `idLocalProject`,`projects`.`ginisOrAthena` AS `ginisOrAthena`,`projects`.`noteGinisOrAthena` AS `noteGinisOrAthena`,`projects`.`deletedDate` AS `deletedDate`,`projects`.`deleteAuthor` AS `deleteAuthor`,`project2area`.`idArea` AS `idArea`,`projects`.`inConcept` AS `inConcept`,`projects`.`dateEvidence` AS `dateEvidence`,`project2communication`.`idCommunication` AS `idCommunication`,`project2communication`.`stationingFrom` AS `stationingFrom`,`project2communication`.`stationingTo` AS `stationingTo`,`project2communication`.`gpsN1` AS `gpsN1`,`project2communication`.`gpsN2` AS `gpsN2`,`project2communication`.`gpsE1` AS `gpsE1`,`project2communication`.`gpsE2` AS `gpsE2`,`supervisorCompany`.`idCompany` AS `supervisorCompanyId`,`buildCompany`.`idCompany` AS `buildCompanyId`,`projectCompany`.`idCompany` AS `projectCompanyId`,if(exists(select 1 from `suspensions` where `suspensions`.`idProject` = `projects`.`idProject` and `suspensions`.`deleted` is null limit 1),1,0) AS `suspended`,`projectsPriorityScore`.`priorityScore` AS `priorityScore`,`projectsPriorityScore`.`correctionValue` AS `correctionValue`,`users`.`idOu` AS `idOu`,if(exists(select 1 from `projectsToPublish` where `projectsToPublish`.`idProject` = `projects`.`idProject` and `projectsToPublish`.`deletedAt` is null limit 1),1,0) AS `published`,`zarukaTechnologicka`.`value` AS `technologyWarranty`,`zarukaStavebni`.`value` AS `constructionWarranty` from ((((((((((`projects` left join `project2area` on(`projects`.`idProject` = `project2area`.`idProject`)) left join `project2communication` on(`projects`.`idProject` = `project2communication`.`idProject`)) left join `projectsPriorityScore` on(`projects`.`idProject` = `projectsPriorityScore`.`idProject`)) left join `project2contact` on(`projects`.`idProject` = `project2contact`.`idProject`)) left join `users` on(`projects`.`editor` = `users`.`username`)) left join `project2company` `supervisorCompany` on(`projects`.`idProject` = `supervisorCompany`.`idProject` and `supervisorCompany`.`idCompanyType` = 3)) left join `project2company` `buildCompany` on(`projects`.`idProject` = `buildCompany`.`idProject` and `buildCompany`.`idCompanyType` = 2)) left join `project2company` `projectCompany` on(`projects`.`idProject` = `projectCompany`.`idProject` and `projectCompany`.`idCompanyType` = 1)) left join `deadlines` `zarukaTechnologicka` on(`projects`.`idProject` = `zarukaTechnologicka`.`idProject` and `zarukaTechnologicka`.`idDeadlineType` = 25)) left join `deadlines` `zarukaStavebni` on(`projects`.`idProject` = `zarukaStavebni`.`idProject` and `zarukaStavebni`.`idDeadlineType` = 26)) where `projects`.`deletedDate` is null) `pr` join `rangePhases` `rp` on(`pr`.`idPhase` = `rp`.`idPhase`)) ;

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `actionsLogs`
--
ALTER TABLE `actionsLogs`
  ADD PRIMARY KEY (`idAction`),
  ADD KEY `FK_Reference_29` (`idActionType`),
  ADD KEY `FK_Reference_31` (`username`),
  ADD KEY `idLocalProject` (`idLocalProject`);

--
-- Indexy pro tabulku `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`idObject`,`idAttributeType`),
  ADD UNIQUE KEY `idObject` (`idObject`,`idAttributeType`),
  ADD KEY `FK_Reference_44` (`idAttributeType`);

--
-- Indexy pro tabulku `calendarEvents`
--
ALTER TABLE `calendarEvents`
  ADD PRIMARY KEY (`idEvent`),
  ADD KEY `FK_Reference_28` (`username`);

--
-- Indexy pro tabulku `collaborator`
--
ALTER TABLE `collaborator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_Reference_39` (`username`),
  ADD KEY `FK_Reference_40` (`collaborator`);

--
-- Indexy pro tabulku `deadlines`
--
ALTER TABLE `deadlines`
  ADD PRIMARY KEY (`idProject`,`idDeadlineType`),
  ADD KEY `idDeadlineType` (`idDeadlineType`);

--
-- Indexy pro tabulku `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`idLogin`);

--
-- Indexy pro tabulku `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`idNotification`),
  ADD KEY `FK_Reference_45` (`username`),
  ADD KEY `FK_Reference_46` (`idAction`);

--
-- Indexy pro tabulku `objects`
--
ALTER TABLE `objects`
  ADD PRIMARY KEY (`idObject`),
  ADD KEY `FK_Reference_18` (`idObjectType`),
  ADD KEY `FK_Reference_17` (`idProject`);

--
-- Indexy pro tabulku `objectType2Attribute`
--
ALTER TABLE `objectType2Attribute`
  ADD PRIMARY KEY (`idObjectType`,`idAttribute`,`idPhase`),
  ADD KEY `FK_Reference_42` (`idAttribute`),
  ADD KEY `idObjecType` (`idObjectType`),
  ADD KEY `idPhase` (`idPhase`);

--
-- Indexy pro tabulku `ou`
--
ALTER TABLE `ou`
  ADD PRIMARY KEY (`idOu`);

--
-- Indexy pro tabulku `prices`
--
ALTER TABLE `prices`
  ADD PRIMARY KEY (`idPriceType`,`idProject`),
  ADD KEY `FK_Reference_6` (`idProject`);

--
-- Indexy pro tabulku `project2area`
--
ALTER TABLE `project2area`
  ADD PRIMARY KEY (`idProject`,`idArea`),
  ADD UNIQUE KEY `idProject` (`idProject`,`idArea`),
  ADD KEY `FK_Reference_25` (`idArea`);

--
-- Indexy pro tabulku `project2communication`
--
ALTER TABLE `project2communication`
  ADD PRIMARY KEY (`idProject2communication`),
  ADD KEY `FK_Reference_9` (`idCommunication`),
  ADD KEY `FK_Reference_8` (`idProject`);

--
-- Indexy pro tabulku `project2company`
--
ALTER TABLE `project2company`
  ADD PRIMARY KEY (`idProject`,`idCompany`,`idCompanyType`),
  ADD KEY `idCompany` (`idCompany`),
  ADD KEY `idCompanyType` (`idCompanyType`);

--
-- Indexy pro tabulku `project2contact`
--
ALTER TABLE `project2contact`
  ADD PRIMARY KEY (`idProject`,`idContactType`),
  ADD KEY `FK_Reference_12` (`idContact`),
  ADD KEY `FK_Reference_37` (`idContactType`);

--
-- Indexy pro tabulku `projectRelations`
--
ALTER TABLE `projectRelations`
  ADD PRIMARY KEY (`idRelation`),
  ADD UNIQUE KEY `idProject` (`idProject`,`idProjectRelation`,`idRelationType`),
  ADD KEY `FK_Reference_33` (`username`),
  ADD KEY `FK_Reference_35` (`idRelationType`),
  ADD KEY `FK_Reference_36` (`idProjectRelation`);

--
-- Indexy pro tabulku `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`idProject`),
  ADD KEY `FK_Reference_27` (`author`),
  ADD KEY `FK_Reference_13` (`editor`),
  ADD KEY `FK_Reference_21` (`idProjectSubtype`),
  ADD KEY `FK_Reference_23` (`idFinSource`),
  ADD KEY `FK_Reference_26` (`idPhase`),
  ADD KEY `FK_Reference_3` (`idProjectType`),
  ADD KEY `FK_Reference_32` (`idLocalProject`),
  ADD KEY `FK_Reference_56` (`deleteAuthor`);

--
-- Indexy pro tabulku `projects2documents`
--
ALTER TABLE `projects2documents`
  ADD PRIMARY KEY (`idDocumentLocal`),
  ADD KEY `FK_Reference_48` (`restoredFrom`),
  ADD KEY `FK_Reference_49` (`documentAuthor`),
  ADD KEY `FK_Reference_51` (`idDocumentCategory`),
  ADD KEY `idDocType` (`idDocType`),
  ADD KEY `idDocument` (`idDocument`),
  ADD KEY `FK_Reference_50` (`idProject`);

--
-- Indexy pro tabulku `projectsToPublish`
--
ALTER TABLE `projectsToPublish`
  ADD PRIMARY KEY (`idProject`),
  ADD UNIQUE KEY `idProject` (`idProject`),
  ADD KEY `publishBy` (`publishBy`);

--
-- Indexy pro tabulku `projectSubtypes2ObjectTypes`
--
ALTER TABLE `projectSubtypes2ObjectTypes`
  ADD PRIMARY KEY (`idProjectSubtypes2ObjectTypes`),
  ADD KEY `idObjectType` (`idObjectType`),
  ADD KEY `idProjectSubtype` (`idProjectSubtype`);

--
-- Indexy pro tabulku `projectVersions`
--
ALTER TABLE `projectVersions`
  ADD PRIMARY KEY (`idLocalProject`),
  ADD KEY `FK_Reference_38` (`idPhase`);

--
-- Indexy pro tabulku `rangeActionTypes`
--
ALTER TABLE `rangeActionTypes`
  ADD PRIMARY KEY (`idActionType`);

--
-- Indexy pro tabulku `rangeAreas`
--
ALTER TABLE `rangeAreas`
  ADD PRIMARY KEY (`idArea`);

--
-- Indexy pro tabulku `rangeAttributesGroups`
--
ALTER TABLE `rangeAttributesGroups`
  ADD PRIMARY KEY (`idAttGroup`);

--
-- Indexy pro tabulku `rangeAttributeTypes`
--
ALTER TABLE `rangeAttributeTypes`
  ADD PRIMARY KEY (`idAttributeType`);

--
-- Indexy pro tabulku `rangeAuthoritiyTypes`
--
ALTER TABLE `rangeAuthoritiyTypes`
  ADD PRIMARY KEY (`idAuthorityType`);

--
-- Indexy pro tabulku `rangeCommunications`
--
ALTER TABLE `rangeCommunications`
  ADD PRIMARY KEY (`idCommunication`),
  ADD KEY `FK_Reference_22` (`idCommunicationType`);

--
-- Indexy pro tabulku `rangeCommunicationTypes`
--
ALTER TABLE `rangeCommunicationTypes`
  ADD PRIMARY KEY (`idCommunicationType`);

--
-- Indexy pro tabulku `rangeCompanies`
--
ALTER TABLE `rangeCompanies`
  ADD PRIMARY KEY (`idCompany`);

--
-- Indexy pro tabulku `rangeCompanyTypes`
--
ALTER TABLE `rangeCompanyTypes`
  ADD PRIMARY KEY (`idCompanyType`);

--
-- Indexy pro tabulku `rangeContacts`
--
ALTER TABLE `rangeContacts`
  ADD PRIMARY KEY (`idContact`);

--
-- Indexy pro tabulku `rangeContactTypes`
--
ALTER TABLE `rangeContactTypes`
  ADD PRIMARY KEY (`idContactType`);

--
-- Indexy pro tabulku `rangeDeadlineTypes`
--
ALTER TABLE `rangeDeadlineTypes`
  ADD PRIMARY KEY (`idDeadlineType`);

--
-- Indexy pro tabulku `rangeDocumentCategories`
--
ALTER TABLE `rangeDocumentCategories`
  ADD PRIMARY KEY (`idDocumentCategory`);

--
-- Indexy pro tabulku `rangeDocumentMimeTypes`
--
ALTER TABLE `rangeDocumentMimeTypes`
  ADD PRIMARY KEY (`extension`);

--
-- Indexy pro tabulku `rangeDocumentTypes`
--
ALTER TABLE `rangeDocumentTypes`
  ADD PRIMARY KEY (`idDocType`);

--
-- Indexy pro tabulku `rangeFinancialSources`
--
ALTER TABLE `rangeFinancialSources`
  ADD PRIMARY KEY (`idFinSource`);

--
-- Indexy pro tabulku `rangeObjectTypes`
--
ALTER TABLE `rangeObjectTypes`
  ADD PRIMARY KEY (`idObjectType`);

--
-- Indexy pro tabulku `rangePhases`
--
ALTER TABLE `rangePhases`
  ADD PRIMARY KEY (`idPhase`);

--
-- Indexy pro tabulku `rangePriceSubtype`
--
ALTER TABLE `rangePriceSubtype`
  ADD PRIMARY KEY (`idPriceSubtypes`);

--
-- Indexy pro tabulku `rangePriceTypes`
--
ALTER TABLE `rangePriceTypes`
  ADD PRIMARY KEY (`idPriceType`),
  ADD KEY `idPriceSubtype` (`idPriceSubtype`);

--
-- Indexy pro tabulku `rangePriorityScaleConfig`
--
ALTER TABLE `rangePriorityScaleConfig`
  ADD PRIMARY KEY (`idPriorityConfig`);

--
-- Indexy pro tabulku `rangeProjectSubtypes`
--
ALTER TABLE `rangeProjectSubtypes`
  ADD PRIMARY KEY (`idProjectSubtype`);

--
-- Indexy pro tabulku `rangeProjectTypes`
--
ALTER TABLE `rangeProjectTypes`
  ADD PRIMARY KEY (`idProjectType`);

--
-- Indexy pro tabulku `rangeRelationTypes`
--
ALTER TABLE `rangeRelationTypes`
  ADD PRIMARY KEY (`idRelationType`);

--
-- Indexy pro tabulku `rangeRoleTypes`
--
ALTER TABLE `rangeRoleTypes`
  ADD PRIMARY KEY (`idRoleType`);

--
-- Indexy pro tabulku `rangeSuspensionReasons`
--
ALTER TABLE `rangeSuspensionReasons`
  ADD PRIMARY KEY (`idSuspensionReason`);

--
-- Indexy pro tabulku `rangeSuspensionSources`
--
ALTER TABLE `rangeSuspensionSources`
  ADD PRIMARY KEY (`idSuspensionSource`);

--
-- Indexy pro tabulku `rangeTags`
--
ALTER TABLE `rangeTags`
  ADD PRIMARY KEY (`idTag`),
  ADD KEY `idTag` (`idTag`);

--
-- Indexy pro tabulku `rangeTaskStatuses`
--
ALTER TABLE `rangeTaskStatuses`
  ADD PRIMARY KEY (`idTaskStatus`);

--
-- Indexy pro tabulku `rangeWarranties`
--
ALTER TABLE `rangeWarranties`
  ADD PRIMARY KEY (`idWarranty`),
  ADD KEY `idWarrantyType` (`idWarrantyType`);

--
-- Indexy pro tabulku `rangeWarrantiesTypes`
--
ALTER TABLE `rangeWarrantiesTypes`
  ADD PRIMARY KEY (`idWarrantyType`);

--
-- Indexy pro tabulku `reportConfig`
--
ALTER TABLE `reportConfig`
  ADD PRIMARY KEY (`idReportConfig`);

--
-- Indexy pro tabulku `reportsHistory`
--
ALTER TABLE `reportsHistory`
  ADD PRIMARY KEY (`idReportHistory`);

--
-- Indexy pro tabulku `suspensions`
--
ALTER TABLE `suspensions`
  ADD PRIMARY KEY (`idSuspension`),
  ADD KEY `FK_Reference_53` (`idSuspensionSource`),
  ADD KEY `FK_Reference_55` (`idSuspensionReason`),
  ADD KEY `FK_Reference_52` (`idProject`);

--
-- Indexy pro tabulku `tags2documents`
--
ALTER TABLE `tags2documents`
  ADD PRIMARY KEY (`idTag`,`idDocument`),
  ADD KEY `idDocument` (`idDocument`),
  ADD KEY `idTag` (`idTag`);

--
-- Indexy pro tabulku `taskReactions`
--
ALTER TABLE `taskReactions`
  ADD PRIMARY KEY (`idTask`,`created`);

--
-- Indexy pro tabulku `tasksProject`
--
ALTER TABLE `tasksProject`
  ADD PRIMARY KEY (`idTask`);

--
-- Indexy pro tabulku `taskVersions`
--
ALTER TABLE `taskVersions`
  ADD PRIMARY KEY (`idTask`,`created`);

--
-- Indexy pro tabulku `type2subtype`
--
ALTER TABLE `type2subtype`
  ADD PRIMARY KEY (`idProjectType`,`idProjectSubtype`),
  ADD KEY `FK_Reference_20` (`idProjectSubtype`),
  ADD KEY `idTypeProject` (`idProjectType`),
  ADD KEY `type2subtype_ibfk_1` (`idPriorityConfig`);

--
-- Indexy pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`),
  ADD KEY `FK_Reference_14` (`idOu`),
  ADD KEY `FK_Reference_16` (`idRoleType`),
  ADD KEY `FK_Reference_47` (`idAuthorityType`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `actionsLogs`
--
ALTER TABLE `actionsLogs`
  MODIFY `idAction` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `calendarEvents`
--
ALTER TABLE `calendarEvents`
  MODIFY `idEvent` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `collaborator`
--
ALTER TABLE `collaborator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `logins`
--
ALTER TABLE `logins`
  MODIFY `idLogin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `notifications`
--
ALTER TABLE `notifications`
  MODIFY `idNotification` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `objects`
--
ALTER TABLE `objects`
  MODIFY `idObject` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `ou`
--
ALTER TABLE `ou`
  MODIFY `idOu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `project2communication`
--
ALTER TABLE `project2communication`
  MODIFY `idProject2communication` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `projectRelations`
--
ALTER TABLE `projectRelations`
  MODIFY `idRelation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `projects`
--
ALTER TABLE `projects`
  MODIFY `idProject` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `projects2documents`
--
ALTER TABLE `projects2documents`
  MODIFY `idDocumentLocal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `projectSubtypes2ObjectTypes`
--
ALTER TABLE `projectSubtypes2ObjectTypes`
  MODIFY `idProjectSubtypes2ObjectTypes` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `projectVersions`
--
ALTER TABLE `projectVersions`
  MODIFY `idLocalProject` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeActionTypes`
--
ALTER TABLE `rangeActionTypes`
  MODIFY `idActionType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeAreas`
--
ALTER TABLE `rangeAreas`
  MODIFY `idArea` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeAttributesGroups`
--
ALTER TABLE `rangeAttributesGroups`
  MODIFY `idAttGroup` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeAttributeTypes`
--
ALTER TABLE `rangeAttributeTypes`
  MODIFY `idAttributeType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeAuthoritiyTypes`
--
ALTER TABLE `rangeAuthoritiyTypes`
  MODIFY `idAuthorityType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeCommunications`
--
ALTER TABLE `rangeCommunications`
  MODIFY `idCommunication` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeCommunicationTypes`
--
ALTER TABLE `rangeCommunicationTypes`
  MODIFY `idCommunicationType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeCompanies`
--
ALTER TABLE `rangeCompanies`
  MODIFY `idCompany` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeCompanyTypes`
--
ALTER TABLE `rangeCompanyTypes`
  MODIFY `idCompanyType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeContacts`
--
ALTER TABLE `rangeContacts`
  MODIFY `idContact` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeContactTypes`
--
ALTER TABLE `rangeContactTypes`
  MODIFY `idContactType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeDeadlineTypes`
--
ALTER TABLE `rangeDeadlineTypes`
  MODIFY `idDeadlineType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeDocumentCategories`
--
ALTER TABLE `rangeDocumentCategories`
  MODIFY `idDocumentCategory` int(11) NOT NULL AUTO_INCREMENT COMMENT 'NEPRECISLOVAVAT ID, v KODU JSOU NA NE ODKAZY';

--
-- AUTO_INCREMENT pro tabulku `rangeDocumentTypes`
--
ALTER TABLE `rangeDocumentTypes`
  MODIFY `idDocType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeFinancialSources`
--
ALTER TABLE `rangeFinancialSources`
  MODIFY `idFinSource` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeObjectTypes`
--
ALTER TABLE `rangeObjectTypes`
  MODIFY `idObjectType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangePhases`
--
ALTER TABLE `rangePhases`
  MODIFY `idPhase` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangePriceSubtype`
--
ALTER TABLE `rangePriceSubtype`
  MODIFY `idPriceSubtypes` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangePriceTypes`
--
ALTER TABLE `rangePriceTypes`
  MODIFY `idPriceType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangePriorityScaleConfig`
--
ALTER TABLE `rangePriorityScaleConfig`
  MODIFY `idPriorityConfig` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeProjectSubtypes`
--
ALTER TABLE `rangeProjectSubtypes`
  MODIFY `idProjectSubtype` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeProjectTypes`
--
ALTER TABLE `rangeProjectTypes`
  MODIFY `idProjectType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeRelationTypes`
--
ALTER TABLE `rangeRelationTypes`
  MODIFY `idRelationType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeRoleTypes`
--
ALTER TABLE `rangeRoleTypes`
  MODIFY `idRoleType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeSuspensionReasons`
--
ALTER TABLE `rangeSuspensionReasons`
  MODIFY `idSuspensionReason` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeSuspensionSources`
--
ALTER TABLE `rangeSuspensionSources`
  MODIFY `idSuspensionSource` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeTags`
--
ALTER TABLE `rangeTags`
  MODIFY `idTag` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeTaskStatuses`
--
ALTER TABLE `rangeTaskStatuses`
  MODIFY `idTaskStatus` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeWarranties`
--
ALTER TABLE `rangeWarranties`
  MODIFY `idWarranty` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `rangeWarrantiesTypes`
--
ALTER TABLE `rangeWarrantiesTypes`
  MODIFY `idWarrantyType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `reportConfig`
--
ALTER TABLE `reportConfig`
  MODIFY `idReportConfig` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `reportsHistory`
--
ALTER TABLE `reportsHistory`
  MODIFY `idReportHistory` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `suspensions`
--
ALTER TABLE `suspensions`
  MODIFY `idSuspension` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `tasksProject`
--
ALTER TABLE `tasksProject`
  MODIFY `idTask` int(11) NOT NULL AUTO_INCREMENT;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `actionsLogs`
--
ALTER TABLE `actionsLogs`
  ADD CONSTRAINT `FK_Reference_29` FOREIGN KEY (`idActionType`) REFERENCES `rangeActionTypes` (`idActionType`),
  ADD CONSTRAINT `FK_Reference_31` FOREIGN KEY (`username`) REFERENCES `users` (`username`);

--
-- Omezení pro tabulku `attributes`
--
ALTER TABLE `attributes`
  ADD CONSTRAINT `FK_Reference_43` FOREIGN KEY (`idObject`) REFERENCES `objects` (`idObject`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Reference_44` FOREIGN KEY (`idAttributeType`) REFERENCES `rangeAttributeTypes` (`idAttributeType`);

--
-- Omezení pro tabulku `calendarEvents`
--
ALTER TABLE `calendarEvents`
  ADD CONSTRAINT `FK_Reference_28` FOREIGN KEY (`username`) REFERENCES `users` (`username`);

--
-- Omezení pro tabulku `collaborator`
--
ALTER TABLE `collaborator`
  ADD CONSTRAINT `FK_Reference_39` FOREIGN KEY (`username`) REFERENCES `users` (`username`),
  ADD CONSTRAINT `FK_Reference_40` FOREIGN KEY (`collaborator`) REFERENCES `users` (`username`);

--
-- Omezení pro tabulku `deadlines`
--
ALTER TABLE `deadlines`
  ADD CONSTRAINT `FK_Reference_2` FOREIGN KEY (`idProject`) REFERENCES `projects` (`idProject`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `deadlines_ibfk_1` FOREIGN KEY (`idDeadlineType`) REFERENCES `rangeDeadlineTypes` (`idDeadlineType`);

--
-- Omezení pro tabulku `objects`
--
ALTER TABLE `objects`
  ADD CONSTRAINT `FK_Reference_17` FOREIGN KEY (`idProject`) REFERENCES `projects` (`idProject`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Reference_18` FOREIGN KEY (`idObjectType`) REFERENCES `rangeObjectTypes` (`idObjectType`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
