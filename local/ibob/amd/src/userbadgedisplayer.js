// $(document).ready(function(){
//     $(".ibob-badge").click(function(){
//         $('#diaglogshowbadge').modal('toggle');
//     });
// });
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * AJAX helper for the tag management page.
 *
 * @module     core/tag
 * @package    core_tag
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.0
 */
define([
    'jquery',
    'core/ajax',
    'core/templates',
    'core/notification',
    'core/str',
    'core/modal_factory',
    'core/modal_events',
    'core/pending',
], function(
    $,
    ajax,
    templates,
    notification,
    str,
    ModalFactory,
    ModalEvents,
    Pending
) {
    return /** @alias module:core/tag */ {

        /**
         * Initialises tag management page.
         *
         * @method initManagePage
         */
        initBadge: function(args) {

            str.get_strings([
                {key: 'modalBadgeDetail', component: 'local_ibob'},
                {key: 'continue', component: 'core'}
            ])
                .then(function(langStrings) {
                    var modalTitle = langStrings[0];
                    var trigger = $('#'+args.badgeId);
                    return ModalFactory.create({
                        title: modalTitle,
                        body: templates.render('local_ibob/userbadgedisplayer', args.context),
                    }, trigger)
                        .done(function(modal) {
                            // modal.show();
                        })
                });
        }
    }
});
