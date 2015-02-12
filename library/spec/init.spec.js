/*global Medium, describe, it, expect, spyOn,
         afterEach, beforeEach, tearDown*/

describe('Initialization TestCase', function () {
    'use strict';

    describe('Objects', function () {
        it('should call init when instantiated', function () {
            spyOn(Medium.prototype, 'init');
            var editor = new Medium('.test');
            expect(editor.init).toHaveBeenCalled();
        });

        it('should accept multiple instances', function () {
            spyOn(Medium.prototype, 'init');
            var editor1 = new Medium('.test'),
                editor2 = new Medium('.test');
            expect(editor1 === editor2).toBe(false);
            expect(Medium.prototype.init).toHaveBeenCalled();
            expect(Medium.prototype.init.calls.count()).toBe(2);
        });

        it('should do nothing when selector does not return any elements', function () {
            spyOn(Medium.prototype, 'initElements');
            spyOn(Medium.prototype, 'initToolbar');
            spyOn(Medium.prototype, 'bindSelect');
            spyOn(Medium.prototype, 'bindButtons');
            spyOn(Medium.prototype, 'bindAnchorForm');
            var editor = new Medium('.test');
            expect(editor.id).toBe(undefined);
            expect(editor.initElements).not.toHaveBeenCalled();
            expect(editor.initToolbar).not.toHaveBeenCalled();
            expect(editor.bindSelect).not.toHaveBeenCalled();
            expect(editor.bindButtons).not.toHaveBeenCalled();
            expect(editor.bindAnchorForm).not.toHaveBeenCalled();
            expect(editor.initElements).not.toHaveBeenCalled();
        });
    });

    describe('Elements', function () {
        it('should allow a string as parameter', function () {
            spyOn(document, 'querySelectorAll').and.callThrough();
            (function () {
                return new Medium('.test');
            }());
            expect(document.querySelectorAll).toHaveBeenCalled();
        });

        it('should allow a list of html elements as parameters', function () {
            var elements = document.querySelectorAll('span'),
                editor = new Medium(elements);
            expect(editor.elements.length).toEqual(elements.length);
        });

        it('should allow a single element as parameter', function () {
            var element = document.querySelector('span'),
                editor = new Medium(element);
            expect(editor.elements).toEqual([element]);
        });

        it('should always initalize elements as an Array', function () {
            var nodeList = document.querySelectorAll('span'),
                node = document.querySelector('span'),
                editor = new Medium(nodeList);

            // nodeList is a NodeList, similar to an array but not of the same type
            expect(editor.elements.length).toEqual(nodeList.length);
            expect(typeof nodeList.forEach).toBe('undefined');
            expect(typeof editor.elements.forEach).toBe('function');
            editor.deactivate();

            editor = new Medium('span');
            expect(editor.elements.length).toEqual(nodeList.length);
            editor.deactivate();

            editor = new Medium(node);
            expect(editor.elements.length).toEqual(1);
            expect(editor.elements[0]).toBe(node);
            editor.deactivate();

            editor = new Medium();
            expect(editor.elements).not.toBe(null);
            expect(editor.elements.length).toBe(0);
            editor.deactivate();
        });
    });

    describe('With a valid element', function () {
        beforeEach(function () {
            this.el = document.createElement('div');
            this.el.className = 'editor';
            document.body.appendChild(this.el);
        });

        afterEach(function () {
            tearDown(this.el);
        });

        it('should have a default set of options', function () {
            var defaultOptions = {
                anchorInputPlaceholder: 'Paste or type a link',
                anchorInputCheckboxLabel: 'Open in new window',
                delay: 0,
                diffLeft: 0,
                diffTop: -10,
                disableReturn: false,
                disableDoubleReturn: false,
                disableEditing: false,
                disableToolbar: false,
                disableAnchorForm: false,
                disablePlaceholders: false,
                elementsContainer: document.body,
                imageDragging: true,
                standardizeSelectionStart: false,
                contentWindow: window,
                ownerDocument: document,
                firstHeader: 'h3',
                forcePlainText: true,
                cleanPastedHTML: false,
                allowMultiParagraphSelection: true,
                placeholder: 'Type your text',
                secondHeader: 'h4',
                buttons: ['bold', 'italic', 'underline', 'anchor', 'header1', 'header2', 'quote'],
                buttonLabels: false,
                targetBlank: false,
                anchorTarget: false,
                anchorButton: false,
                anchorButtonClass: 'btn',
                anchorPreviewHideDelay: 500,
                checkLinkFormat: false,
                extensions: {},
                activeButtonClass: 'medium-editor-button-active',
                firstButtonClass: 'medium-editor-button-first',
                lastButtonClass: 'medium-editor-button-last'
            },
                editor = new Medium('.editor');
            expect(editor.options).toEqual(defaultOptions);
        });

        it('should accept custom options values', function () {
            var options = {
                anchorInputPlaceholder: 'test',
                anchorInputCheckboxLabel: 'new window?',
                diffLeft: 10,
                diffTop: 5,
                firstHeader: 'h2',
                secondHeader: 'h3',
                delay: 300
            },
                editor = new Medium('.editor', options);
            expect(editor.options).toEqual(options);
        });

        it('should call the default initialization methods', function () {
            spyOn(Medium.prototype, 'initElements').and.callThrough();
            spyOn(Medium.prototype, 'initToolbar').and.callThrough();
            spyOn(Medium.prototype, 'bindSelect').and.callThrough();
            spyOn(Medium.prototype, 'bindButtons').and.callThrough();
            spyOn(Medium.prototype, 'bindAnchorForm').and.callThrough();
            var editor = new Medium('.editor');
            expect(editor.id).toBe(1);
            expect(editor.initElements).toHaveBeenCalled();
            expect(editor.initToolbar).toHaveBeenCalled();
            expect(editor.bindSelect).toHaveBeenCalled();
            expect(editor.bindButtons).toHaveBeenCalled();
            expect(editor.bindAnchorForm).toHaveBeenCalled();
            expect(editor.initElements).toHaveBeenCalled();
        });

        it('should set the ID according to the numbers of editors instantiated', function () {
            var editor1 = new Medium('.editor'),
                editor2 = new Medium('.editor'),
                editor3 = new Medium('.editor');
            expect(editor1.id).toBe(1);
            expect(editor2.id).toBe(2);
            expect(editor3.id).toBe(3);
        });

        it('should not reset ID when deactivated and then re-initialized', function () {
            var secondEditor = document.createElement('div'),
                editor1 = new Medium('.editor'),
                editor2;

            secondEditor.className = 'editor-two';
            document.body.appendChild(secondEditor);

            editor2 = new Medium('.editor-two');
            editor1.deactivate();
            editor1.init('.editor');

            expect(editor1.id).not.toEqual(editor2.id);
        });

        it('should use document.body as element container when no container element is specified', function () {
            spyOn(document.body, 'appendChild').and.callThrough();
            (function () {
                return new Medium('.editor');
            }());
            expect(document.body.appendChild).toHaveBeenCalled();
        });

        it('should accept a custom element container for Medium elements', function () {
            var div = document.createElement('div');
            document.body.appendChild(div);
            spyOn(div, 'appendChild').and.callThrough();
            (function () {
                return new Medium('.editor', {
                    elementsContainer: div
                });
            }());
            expect(div.appendChild).toHaveBeenCalled();
            document.body.removeChild(div);
        });
    });
});
