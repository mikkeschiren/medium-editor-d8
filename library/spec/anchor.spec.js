/*global Medium, describe, it, expect, spyOn,
     afterEach, beforeEach, selectElementContents,
     jasmine, fireEvent, console, tearDown,
     selectElementContentsAndFire, xit */

describe('Anchor Button TestCase', function () {
    'use strict';

    beforeEach(function () {
        jasmine.clock().install();
        this.el = document.createElement('div');
        this.el.className = 'editor';
        this.el.innerHTML = 'lorem ipsum';
        document.body.appendChild(this.el);
    });

    afterEach(function () {
        tearDown(this.el);
        jasmine.clock().uninstall();
    });

    describe('Click', function () {
        it('should display the anchor form when toolbar is visible', function () {
            spyOn(Medium.prototype, 'showAnchorForm').and.callThrough();
            var button,
                editor = new Medium('.editor');
            selectElementContentsAndFire(editor.elements[0]);
            jasmine.clock().tick(1);
            button = editor.toolbar.querySelector('[data-action="anchor"]');
            fireEvent(button, 'click');
            expect(editor.toolbarActions.style.display).toBe('none');
            expect(editor.anchorForm.style.display).toBe('block');
            expect(editor.showAnchorForm).toHaveBeenCalled();
        });

        it('should display the toolbar actions when anchor form is visible', function () {
            spyOn(Medium.prototype, 'showToolbarActions').and.callThrough();
            var button,
                editor = new Medium('.editor');
            selectElementContentsAndFire(editor.elements[0]);
            jasmine.clock().tick(11); // checkSelection delay
            button = editor.toolbar.querySelector('[data-action="anchor"]');
            editor.anchorForm.style.display = 'block';
            fireEvent(button, 'click');
            expect(editor.toolbarActions.style.display).toBe('block');
            expect(editor.anchorForm.style.display).toBe('none');
            expect(editor.showToolbarActions).toHaveBeenCalled();
        });

        it('should unlink when selection is a link', function () {
            spyOn(document, 'execCommand').and.callThrough();
            this.el.innerHTML = '<a href="#">link</a>';
            var button,
                editor = new Medium('.editor');
            selectElementContentsAndFire(editor.elements[0]);
            jasmine.clock().tick(11); // checkSelection delay
            button = editor.toolbar.querySelector('[data-action="anchor"]');
            fireEvent(button, 'click');
            expect(this.el.innerHTML, 'link');
            expect(document.execCommand).toHaveBeenCalled();
        });

    });

    describe('Link Creation', function () {
        it('should create a link when user presses enter', function () {
            spyOn(Medium.prototype, 'createLink').and.callThrough();
            var editor = new Medium('.editor'),
                button,
                input;

            selectElementContents(editor.elements[0]);
            button = editor.toolbar.querySelector('[data-action="anchor"]');
            fireEvent(button, 'click');
            input = editor.anchorForm.querySelector('input');
            input.value = 'test';
            fireEvent(input, 'keyup', 13);
            expect(editor.createLink).toHaveBeenCalled();
        });
        it('shouldn\'t create a link when user presses enter without value', function () {
            spyOn(Medium.prototype, 'createLink').and.callThrough();
            var editor = new Medium('.editor'),
                button,
                input;

            selectElementContents(editor.elements[0]);
            button = editor.toolbar.querySelector('[data-action="anchor"]');
            fireEvent(button, 'click');
            input = editor.anchorForm.querySelector('input');
            input.value = '';
            fireEvent(input, 'keyup', 13);
            expect(editor.elements[0].querySelector('a')).toBeNull();
        });
        it('should add http:// if need be and checkLinkFormat option is set to true', function () {
            var editor = new Medium('.editor', {
                checkLinkFormat: true
            });
            selectElementContentsAndFire(editor.elements[0]);
            editor.showAnchorForm('test.com');
            fireEvent(editor.anchorForm.querySelector('a.medium-editor-toobar-save'), 'click');

            expect(editor.elements[0].querySelector('a').href).toBe('http://test.com/');
        });
        it('should not change protocol when a valid one is included', function () {
            var editor = new Medium('.editor', {
                checkLinkFormat: true
            }),
                validUrl = 'mailto:test.com';
            selectElementContentsAndFire(editor.elements[0]);
            editor.showAnchorForm(validUrl);
            fireEvent(editor.anchorForm.querySelector('a.medium-editor-toobar-save'), 'click');

            expect(editor.elements[0].querySelector('a').href).toBe(validUrl);
        });
        it('should add target="_blank" when respective option is set to true', function () {
            var editor = new Medium('.editor', {
                targetBlank: true
            });
            selectElementContentsAndFire(editor.elements[0]);
            editor.showAnchorForm('http://test.com');
            fireEvent(editor.anchorForm.querySelector('a.medium-editor-toobar-save'), 'click');

            expect(editor.elements[0].querySelector('a').target).toBe('_blank');
        });
        it('should create a button when user selects this option and presses enter', function () {
            spyOn(Medium.prototype, 'createLink').and.callThrough();
            var editor = new Medium('.editor', {
                anchorButton: true,
                anchorButtonClass: 'btn btn-default'
            }),
                save,
                input,
                button;

            selectElementContents(editor.elements[0]);
            save = editor.toolbar.querySelector('[data-action="anchor"]');
            fireEvent(save, 'click');

            input = editor.anchorForm.querySelector('input.medium-editor-toolbar-input');
            input.value = 'test';

            button = editor.anchorForm.querySelector('input.medium-editor-toolbar-anchor-button');
            button.setAttribute('type', 'checkbox');
            button.checked = true;

            fireEvent(input, 'keyup', 13);
            expect(editor.createLink).toHaveBeenCalledWith(input, '_self', 'btn btn-default');
            expect(editor.elements[0].querySelector('a').classList.contains('btn')).toBe(true);
            expect(editor.elements[0].querySelector('a').classList.contains('btn-default')).toBe(true);
        });
    });

    describe('Cancel', function () {
        it('should close the link form when user clicks on cancel', function () {
            spyOn(Medium.prototype, 'showToolbarActions').and.callThrough();
            var editor = new Medium('.editor'),
                button,
                cancel;

            selectElementContents(editor.elements[0]);
            button = editor.toolbar.querySelector('[data-action="anchor"]');
            cancel = editor.anchorForm.querySelector('a.medium-editor-toobar-close');
            fireEvent(button, 'click');
            expect(editor.anchorForm.style.display).toBe('block');
            fireEvent(cancel, 'click');
            expect(editor.showToolbarActions).toHaveBeenCalled();
            expect(editor.anchorForm.style.display).toBe('none');
        });
    });

});
