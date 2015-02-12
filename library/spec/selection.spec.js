/*global MediumEditor, describe, it, expect, spyOn,
         afterEach, beforeEach, fireEvent, waits,
         jasmine, selectElementContents, tearDown,
         selectElementContentsAndFire */

describe('Selection TestCase', function () {
    'use strict';

    beforeEach(function () {
        this.el = document.createElement('div');
        this.el.className = 'editor';
        this.el.innerHTML = 'lorem ipsum';
        document.body.appendChild(this.el);
        jasmine.clock().install();
    });

    afterEach(function () {
        tearDown(this.el);
        jasmine.clock().uninstall();
    });

    describe('CheckSelection', function () {
        it('should check for selection on mouseup event', function () {
            spyOn(MediumEditor.prototype, 'checkSelection');
            var editor = new MediumEditor('.editor');
            fireEvent(editor.elements[0], 'mouseup');
            jasmine.clock().tick(11); // checkSelection delay
            expect(editor.checkSelection).toHaveBeenCalled();
        });

        it('should check for selection on keyup', function () {
            spyOn(MediumEditor.prototype, 'checkSelection');
            var editor = new MediumEditor('.editor');
            fireEvent(editor.elements[0], 'keyup');
            jasmine.clock().tick(11); // checkSelection delay
            expect(editor.checkSelection).toHaveBeenCalled();
        });

        it('should do nothing when keepToolbarAlive is true', function () {
            spyOn(window, 'getSelection').and.callThrough();
            var editor = new MediumEditor('.editor');
            editor.keepToolbarAlive = true;
            editor.checkSelection();
            expect(window.getSelection).not.toHaveBeenCalled();
        });

        describe('When keepToolbarAlive is false', function () {
            it('should hide the toolbar if selection is empty', function () {
                spyOn(MediumEditor.prototype, 'setToolbarPosition').and.callThrough();
                spyOn(MediumEditor.prototype, 'setToolbarButtonStates').and.callThrough();
                spyOn(MediumEditor.prototype, 'showToolbarActions').and.callThrough();
                var editor = new MediumEditor('.editor');
                editor.toolbar.style.display = 'block';
                editor.toolbar.classList.add('medium-editor-toolbar-active');
                expect(editor.toolbar.classList.contains('medium-editor-toolbar-active')).toBe(true);
                editor.checkSelection();
                expect(editor.toolbar.classList.contains('medium-editor-toolbar-active')).toBe(false);
                expect(editor.setToolbarPosition).not.toHaveBeenCalled();
                expect(editor.setToolbarButtonStates).not.toHaveBeenCalled();
                expect(editor.showToolbarActions).not.toHaveBeenCalled();
            });

            it('should show the toolbar when something is selected', function () {
                var editor = new MediumEditor('.editor');
                expect(editor.toolbar.classList.contains('medium-editor-toolbar-active')).toBe(false);
                selectElementContentsAndFire(this.el);
                jasmine.clock().tick(501);
                expect(editor.toolbar.classList.contains('medium-editor-toolbar-active')).toBe(true);
            });

            it('should update toolbar position and button states when something is selected', function () {
                spyOn(MediumEditor.prototype, 'setToolbarPosition').and.callThrough();
                spyOn(MediumEditor.prototype, 'setToolbarButtonStates').and.callThrough();
                spyOn(MediumEditor.prototype, 'showToolbarActions').and.callThrough();
                var editor = new MediumEditor('.editor');
                selectElementContentsAndFire(this.el);
                jasmine.clock().tick(51);
                expect(editor.setToolbarPosition).toHaveBeenCalled();
                expect(editor.setToolbarButtonStates).toHaveBeenCalled();
                expect(editor.showToolbarActions).toHaveBeenCalled();
            });

            it('should update button states when updateOnEmptySelection is true and the selection is empty', function () {
                spyOn(MediumEditor.prototype, 'setToolbarButtonStates').and.callThrough();

                var editor = new MediumEditor('.editor', {
                    updateOnEmptySelection: true
                });

                selectElementContentsAndFire(this.el, { collapse: 'toStart' });
                jasmine.clock().tick(51);

                expect(editor.setToolbarButtonStates).toHaveBeenCalled();
            });
        });
    });

});
