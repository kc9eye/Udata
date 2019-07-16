<?php
/* This file is part of UData.
 * Copyright (C) 2018 Paul W. Lane <kc9eye@outlook.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
$objs = [
    #SDS table and required supplements, index and triggers
    'CREATE TABLE sds (
        id varchar primary key,
        name varchar not null,
        used varchar not null,
        dist varchar not null,
        _date timestamp with time zone default now(),
        added_by varchar not null,
        meta varchar,
        search tsvector)',
    'CREATE INDEX sds_search_index ON sds USING GIN (search)',
    'CREATE TRIGGER sds_search_update BEFORE INSERT OR UPDATE
     ON sds FOR EACH ROW EXECUTE PROCEDURE 
     tsvector_update_trigger(search,\'pg_catalog.english\',name,dist,meta)',

    #Safety meeting minutes
    'CREATE TABLE sbm (
        id varchar primary key,
        meeting_date date,
        uid varchar)',
    
    #File and media indexes for both
    'CREATE TABLE file_index (
        id varchar primary key,
        file varchar not null,
        mime varchar not null,
        reference varchar not null,
        ref_id varchar not null,
        upload_name varchar not null,
        _date timestamp with time zone default now(),
        uid varchar not null)',
    
    'CREATE TABLE media (
        id varchar primary key,
        name varchar not null,
        _date timestamp with time zone default now(),
        genre varchar not null,
        subgenre varchar,
        uid varchar not null)'
];