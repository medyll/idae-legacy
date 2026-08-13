"use strict";
var IdaeBe = (() => {
  var __defProp = Object.defineProperty;
  var __defProps = Object.defineProperties;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropDescs = Object.getOwnPropertyDescriptors;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getOwnPropSymbols = Object.getOwnPropertySymbols;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __propIsEnum = Object.prototype.propertyIsEnumerable;
  var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
  var __spreadValues = (a, b) => {
    for (var prop in b || (b = {}))
      if (__hasOwnProp.call(b, prop))
        __defNormalProp(a, prop, b[prop]);
    if (__getOwnPropSymbols)
      for (var prop of __getOwnPropSymbols(b)) {
        if (__propIsEnum.call(b, prop))
          __defNormalProp(a, prop, b[prop]);
      }
    return a;
  };
  var __spreadProps = (a, b) => __defProps(a, __getOwnPropDescs(b));
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);
  var __publicField = (obj, key, value) => {
    __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
    return value;
  };
  var __accessCheck = (obj, member, msg) => {
    if (!member.has(obj))
      throw TypeError("Cannot " + msg);
  };
  var __privateGet = (obj, member, getter) => {
    __accessCheck(obj, member, "read from private field");
    return getter ? getter.call(obj) : member.get(obj);
  };
  var __privateAdd = (obj, member, value) => {
    if (member.has(obj))
      throw TypeError("Cannot add the same private member more than once");
    member instanceof WeakSet ? member.add(obj) : member.set(obj, value);
  };

  // ../../../../idae/packages/idae-be/dist/index.js
  var dist_exports = {};
  __export(dist_exports, {
    AttrHandler: () => AttrHandler,
    Be: () => Be,
    BeUtils: () => BeUtils,
    ClassesHandler: () => ClassesHandler,
    DataHandler: () => DataHandler,
    DomHandler: () => DomHandler,
    DynamicHandler: () => DynamicHandler,
    EventsHandler: () => EventsHandler,
    Fragments: () => Fragments,
    HttpHandler: () => HttpHandler,
    PositionHandler: () => PositionHandler,
    StylesHandler: () => StylesHandler,
    TextHandler: () => TextHandler,
    TimersHandler: () => TimersHandler,
    WalkHandler: () => WalkHandler,
    be: () => be,
    beId: () => beId,
    createBe: () => createBe,
    toBe: () => toBe,
    walkerMethods: () => walkerMethods
  });

  // ../../../../idae/packages/idae-be/dist/modules/attrs.js
  var attrMethods;
  (function(attrMethods2) {
    attrMethods2["set"] = "set";
    attrMethods2["get"] = "get";
    attrMethods2["delete"] = "delete";
  })(attrMethods || (attrMethods = {}));
  var _AttrHandler = class _AttrHandler {
    /**
     * Initializes the AttrHandler with a Be element.
     * @param element - The Be element to operate on.
     */
    constructor(element) {
      __publicField(this, "beElement");
      __publicField(this, "methods", _AttrHandler.methods);
      this.beElement = element;
    }
    /**
     * Handles dynamic method calls for attribute operations.
     * @param actions - The actions to perform (e.g., set, get, delete).
     * @returns The Be element for chaining.
     */
    handle(actions) {
      Object.entries(actions).forEach(([method, props]) => {
        if (method in this) {
          this[method](props);
        }
      });
      return this.beElement;
    }
    /**
     * Retrieves the value of an attribute.
     * @param name - The name of the attribute to retrieve.
     * @returns The value of the attribute, or `null` if not found.
     * @example
     * // HTML: <div id="test" data-role="admin"></div>
     * const beInstance = be('#test');
     * const role = beInstance.getAttr('data-role');
     * console.log(role); // Output: "admin"
     */
    get(name) {
      var _a;
      if (typeof this.beElement.inputNode === "string")
        return ((_a = document.querySelector(this.beElement.inputNode || "")) == null ? void 0 : _a.getAttribute(name || "")) || null;
      if (this.beElement.isWhat !== "element")
        return null;
      const el = this.beElement.inputNode;
      return name ? el.getAttribute(name) : null;
    }
    /**
     * Sets one or more attributes on the element(s).
     * @param nameOrObject - A key-value pair or an object containing multiple key-value pairs.
     * @param value - The value to set if a single key is provided.
     * @returns The Be element for chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.setAttr('data-role', 'admin'); // Sets a single attribute
     * beInstance.setAttr({ class: 'highlight', title: 'Hello' }); // Sets multiple attributes
     */
    set(nameOrObject, value) {
      this.beElement.eachNode((el) => {
        if (typeof nameOrObject === "string" && value !== void 0) {
          el.setAttribute(nameOrObject, value);
        } else if (typeof nameOrObject === "object") {
          Object.entries(nameOrObject).forEach(([name, val]) => {
            el.setAttribute(name, val);
          });
        }
      });
      return this.beElement;
    }
    /**
     * Deletes one or more attributes from the element(s).
     * @param nameOrObject - A key or an object containing multiple keys to delete.
     * @returns The Be element for chaining.
     * @example
     * // HTML: <div id="test" data-role="admin" class="highlight"></div>
     * const beInstance = be('#test');
     * beInstance.deleteAttr('data-role'); // Deletes a single attribute
     * beInstance.deleteAttr({ class: '' }); // Deletes multiple attributes
     */
    delete(nameOrObject) {
      this.beElement.eachNode((el) => {
        if (typeof nameOrObject === "string") {
          el.removeAttribute(nameOrObject);
        } else if (typeof nameOrObject === "object") {
          Object.entries(nameOrObject).forEach(([name]) => {
            el.removeAttribute(name);
          });
        }
      });
      return this.beElement;
    }
    /**
     * Retrieves all attributes as a key-value pair object.
     * @returns An object containing all attributes, or `null` if not applicable.
     * @example
     * // HTML: <div id="test" data-role="admin" class="highlight"></div>
     * const beInstance = be('#test');
     * const attributes = beInstance.attrs.valueOf();
     * console.log(attributes); // Output: { id: "test", "data-role": "admin", class: "highlight" }
     */
    valueOf() {
      if (this.beElement.isWhat !== "element")
        return null;
      const el = this.beElement.inputNode;
      const attrs = {};
      for (let i = 0; i < el.attributes.length; i++) {
        const attr = el.attributes[i];
        attrs[attr.name] = attr.value;
      }
      return attrs;
    }
  };
  __publicField(_AttrHandler, "methods", Object.values(attrMethods));
  var AttrHandler = _AttrHandler;

  // ../../../../idae/packages/idae-be/dist/modules/styles.js
  var beStyleMethods;
  (function(beStyleMethods2) {
    beStyleMethods2["set"] = "set";
    beStyleMethods2["get"] = "get";
    beStyleMethods2["unset"] = "unset";
  })(beStyleMethods || (beStyleMethods = {}));
  var _StylesHandler = class _StylesHandler {
    constructor(beElement) {
      __publicField(this, "beElement");
      __publicField(this, "methods", Object.keys(beStyleMethods));
      this.beElement = beElement;
    }
    valueOf() {
      return `[StylesHandler: ${this.methods.join(", ")}]`;
    }
    handle(actions) {
      const { method, args } = this.resolveIndirection(actions);
      this.beElement.eachNode(() => {
        switch (method) {
          case "set":
            this.set(args);
            break;
          case "get":
            this.get(args);
            break;
          case "unset":
            this.unset(args);
            break;
        }
      });
      return this.beElement;
    }
    resolveIndirection(actions) {
      let method = "get";
      let args;
      Object.keys(actions).forEach((action) => {
        const actionKey = action;
        if (_StylesHandler.methods.includes(actionKey)) {
          method = actionKey;
          args = actions[actionKey];
        }
      });
      return { method, args };
    }
    /**
     * Sets one or more CSS styles for the selected element(s), including CSS custom properties.
     * @param styles - An object of CSS properties and values, or a string of CSS properties and values.
     * @param value - The value for a single CSS property when styles is a property name string.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.setStyle({ color: 'red', backgroundColor: 'blue' }); // Sets multiple styles
     * beInstance.setStyle('color', 'green'); // Sets a single style
     */
    set(styles, value) {
      if (typeof styles === "string") {
        if (styles.includes(":")) {
          this.beElement.eachNode((el) => {
            el.style.cssText = styles;
          });
        } else {
          this.applyStyle(styles, value != null ? value : "");
        }
      } else if (typeof styles === "object") {
        Object.entries(styles).forEach(([prop, val]) => {
          this.applyStyle(prop, val);
        });
      }
      return this.beElement;
    }
    /**
     * Gets the value of a CSS property for the first matched element.
     * @param key - The CSS property name.
     * @returns The value of the CSS property, or null if not found.
     * @example
     * // HTML: <div id="test" style="color: red;"></div>
     * const beInstance = be('#test');
     * const color = beInstance.getStyle('color');
     * console.log(color); // Output: "red"
     */
    get(key) {
      let css = null;
      this.beElement.eachNode((el) => {
        css = el.style[key] || null;
        if (!css) {
          const computedStyle = window.getComputedStyle(el);
          css = computedStyle.getPropertyValue(key).trim();
        }
      }, true);
      return css || null;
    }
    /**
     * Removes a CSS property from the selected element(s).
     * @param key - The CSS property name to remove.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test" style="color: red;"></div>
     * const beInstance = be('#test');
     * beInstance.unsetStyle('color'); // Removes the "color" style
     */
    unset(key) {
      this.beElement.eachNode((el) => {
        el.style.removeProperty(key);
      });
      return this.beElement;
    }
    applyStyle(property, value) {
      this.beElement.eachNode((el) => {
        const kebabProperty = toKebabCase(property);
        el.style.setProperty(kebabProperty, value);
      });
    }
    getKey(key) {
      let value = null;
      this.beElement.eachNode((el) => {
        value = el.style.getPropertyValue(key) || null;
      });
      return value;
    }
  };
  __publicField(_StylesHandler, "methods", Object.values(beStyleMethods));
  var StylesHandler = _StylesHandler;
  function toKebabCase(property) {
    return property.replace(/([a-z])([A-Z])/g, "$1-$2").toLowerCase();
  }

  // ../../../../idae/packages/idae-be/dist/utils.js
  var BeUtils = class _BeUtils {
    static isHTML(str, options) {
      var _a;
      const result = {
        isHtml: false,
        tag: "",
        attributes: {},
        styles: {},
        node: void 0,
        beElem: void 0
      };
      if (str instanceof HTMLElement)
        result.node = str;
      if (typeof str !== "string")
        return result;
      const trimmed = str.trim();
      if (options.transformTextToHtml || trimmed.startsWith("<") && trimmed.endsWith(">") && trimmed.includes("</")) {
        result.isHtml = true;
        const tagMatch = trimmed.match(/<(\w+)/);
        result.tag = (_a = tagMatch == null ? void 0 : tagMatch[1]) != null ? _a : "span";
        const attributesMatch = trimmed.match(/<\w+\s+([^>]+)>/);
        if (attributesMatch) {
          const attributesString = attributesMatch[1];
          const attributeRegex = /(\w+)(?:="([^"]*)")?/g;
          let match;
          while ((match = attributeRegex.exec(attributesString)) !== null) {
            const [, key, value] = match;
            if (key === "style") {
              const styleRegex = /(\w+-?\w+)\s*:\s*([^;]+)/g;
              let styleMatch;
              while ((styleMatch = styleRegex.exec(value)) !== null) {
                const [, styleName, styleValue] = styleMatch;
                result.styles[styleName] = styleValue.trim();
              }
            } else {
              result.attributes[key] = value || "";
            }
          }
        }
        if (result.isHtml && options.returnHTMLelement) {
          const html = str.replace(/<[^>]+>/, "").replace(/<\/[^>]+>$/, "");
          result.beElem = createBe(result.tag);
          result.beElem.update(html);
          if (result.styles)
            result.beElem.setStyle(result.styles);
          const newElement = document.createElement(result.tag);
          newElement.innerHTML = html;
          result.node = newElement;
        }
      }
      if (options.transformTextToHtml && !result.isHtml) {
        const newElement = document.createElement("span");
        newElement.innerHTML = str;
        result.node = newElement;
      }
      return result;
    }
    static calculateAnchorPoint(rect, anchor) {
      let x = rect.left;
      let y = rect.top;
      if (typeof anchor === "string") {
        const [vertical, horizontal] = anchor.split(" ");
        switch (vertical) {
          case "top":
            y = rect.top;
            break;
          case "bottom":
            y = rect.bottom;
            break;
          case "center":
            y = rect.top + rect.height / 2;
            x = rect.left + rect.width / 2;
            break;
        }
        switch (horizontal) {
          case "left":
            x = rect.left;
            break;
          case "right":
            x = rect.right;
            break;
          case "center":
            x = rect.left + rect.width / 2;
            break;
        }
      } else {
        throw new Error("Invalid anchor type. Expected a string.");
      }
      return [x, y];
    }
    static applyStyle(beElement, property, value) {
      beElement.eachNode((el) => {
        el.style.setProperty(property, value);
      });
    }
    static applyCallback(el, callback) {
      if (el instanceof HTMLCollection) {
        return Array.from(el).forEach((ss) => {
          _BeUtils.applyCallback(ss, callback);
        });
      } else {
        return callback(el);
      }
    }
    static resolveIndirection(classHandler, actions) {
      let method;
      let props;
      Object.keys(actions).forEach((action) => {
        if (classHandler.methods.includes(action)) {
          method = action;
          props = actions[action];
        }
      });
      return { method, props };
    }
  };

  // ../../../../idae/packages/idae-be/dist/modules/data.js
  var dataMethods;
  (function(dataMethods2) {
    dataMethods2["set"] = "set";
    dataMethods2["get"] = "get";
    dataMethods2["delete"] = "delete";
    dataMethods2["getKey"] = "getKey";
  })(dataMethods || (dataMethods = {}));
  var _DataHandler = class _DataHandler {
    /**
     * Initializes the DataHandler with a Be element.
     * @param element - The Be element to operate on.
     */
    constructor(element) {
      __publicField(this, "beElement");
      __publicField(this, "methods", _DataHandler.methods);
      this.beElement = element;
    }
    /**
     * Handles dynamic method calls for data operations.
     * @param actions - The action to perform (e.g., set, get, delete).
     * @returns The Be element for chaining.
     */
    handle(actions) {
      const { method, props } = BeUtils.resolveIndirection(this, actions);
      switch (method) {
        case "set":
        case "delete":
          this[method](props.keyOrObject, props.value);
          break;
      }
      return this.beElement;
    }
    /**
     * Retrieves the value of a `data-*` attribute.
     * @param key - The key of the `data-*` attribute to retrieve.
     * @returns The value of the attribute, or `null` if not found.
     * @example
     * // HTML: <div id="test" data-role="admin"></div>
     * const beInstance = be('#test');
     * const role = beInstance.getData('role');
     * console.log(role); // Output: "admin"
     */
    get(key) {
      if (this.beElement.isWhat !== "element")
        return null;
      return this.beElement.inputNode.dataset[key] || null;
    }
    /**
     * Sets one or more `data-*` attributes.
     * @param keyOrObject - A key-value pair or an object containing multiple key-value pairs.
     * @param value - The value to set if a single key is provided.
     * @returns The Be element for chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.setData('role', 'admin'); // Sets a single `data-role` attribute
     * beInstance.setData({ role: 'admin', type: 'user' }); // Sets multiple `data-*` attributes
     */
    set(keyOrObject, value) {
      this.beElement.eachNode((el) => {
        if (typeof keyOrObject === "string" && value !== void 0) {
          el.dataset[keyOrObject] = value;
        } else if (typeof keyOrObject === "object") {
          Object.entries(keyOrObject).forEach(([key, val]) => {
            el.dataset[key] = val;
          });
        }
      });
      return this.beElement;
    }
    /**
     * Deletes one or more `data-*` attributes.
     * @param keyOrObject - A key or an object containing multiple keys to delete.
     * @returns The Be element for chaining.
     * @example
     * // HTML: <div id="test" data-role="admin" data-type="user"></div>
     * const beInstance = be('#test');
     * beInstance.deleteData('role'); // Deletes the `data-role` attribute
     * beInstance.deleteData({ type: '' }); // Deletes the `data-type` attribute
     */
    delete(keyOrObject) {
      this.beElement.eachNode((el) => {
        if (typeof keyOrObject === "string") {
          delete el.dataset[keyOrObject];
        } else if (typeof keyOrObject === "object") {
          Object.keys(keyOrObject).forEach((key) => {
            delete el.dataset[key];
          });
        }
      });
      return this.beElement;
    }
    /**
     * Retrieves the value of a specific `data-*` attribute.
     * @param key - The key of the `data-*` attribute to retrieve.
     * @returns The value of the attribute, or `null` if not found.
     * @example
     * // HTML: <div id="test" data-role="admin"></div>
     * const beInstance = be('#test');
     * const role = beInstance.getKey('role');
     * console.log(role); // Output: "admin"
     */
    getKey(key) {
      if (this.beElement.isWhat !== "element")
        return null;
      return this.beElement.inputNode.dataset[key] || null;
    }
    /**
     * Retrieves all `data-*` attributes as a DOMStringMap.
     * @returns A DOMStringMap containing all `data-*` attributes, or `null` if not applicable.
     * @example
     * // HTML: <div id="test" data-role="admin" data-type="user"></div>
     * const beInstance = be('#test');
     * const dataAttributes = beInstance.data.valueOf();
     * console.log(dataAttributes); // Output: { role: "admin", type: "user" }
     */
    valueOf() {
      if (this.beElement.isWhat !== "element")
        return null;
      return this.beElement.inputNode.dataset;
    }
  };
  __publicField(_DataHandler, "methods", Object.values(dataMethods));
  var DataHandler = _DataHandler;

  // ../../../../idae/packages/idae-be/dist/modules/events.js
  var eventsMethods;
  (function(eventsMethods2) {
    eventsMethods2["on"] = "on";
    eventsMethods2["off"] = "off";
    eventsMethods2["fire"] = "fire";
  })(eventsMethods || (eventsMethods = {}));
  var _EventsHandler = class _EventsHandler {
    constructor(beElement) {
      __publicField(this, "beElement");
      __publicField(this, "methods", _EventsHandler.methods);
      this.beElement = beElement;
    }
    /**
     * Handle event actions (add or remove event listeners).
     * @param actions An object specifying the event actions to perform.
     * @returns The Be instance for method chaining.
     */
    handle(actions) {
      Object.entries(actions).forEach(([method, props]) => {
        const [eventName, handler] = Object.entries(props)[0];
        switch (method) {
          case "on":
          case "off":
            this[method](eventName, handler, props == null ? void 0 : props.options, props == null ? void 0 : props.callback);
            break;
          case "fire":
            this.fire(eventName, props == null ? void 0 : props.detail, props == null ? void 0 : props.options, props == null ? void 0 : props.callback);
            break;
        }
      });
      return this.beElement;
    }
    /**
     * Adds an event listener to the element(s).
     * @param eventName - The name of the event to listen for.
     * @param handler - The event handler function.
     * @param options - Optional event listener options.
     * @param callback - Optional callback function to execute after adding the event listener.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.on('click', () => console.log('Clicked!')); // Adds a click event listener
     */
    on(eventName, handler, options, callback) {
      this.beElement.eachNode((el) => {
        el.addEventListener(eventName, handler, options);
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Removes an event listener from the element(s).
     * @param eventName - The name of the event to remove.
     * @param handler - The event handler function.
     * @param options - Optional event listener options.
     * @param callback - Optional callback function to execute after removing the event listener.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * const handler = () => console.log('Clicked!');
     * beInstance.on('click', handler); // Adds a click event listener
     * beInstance.off('click', handler); // Removes the click event listener
     */
    off(eventName, handler, options, callback) {
      this.beElement.eachNode((el) => {
        el.removeEventListener(eventName, handler, options);
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Dispatches a custom event on the element(s).
     * @param eventName - The name of the custom event to dispatch.
     * @param detail - Optional data to include in the event.
     * @param options - Optional event initialization options.
     * @param callback - Optional callback function to execute after dispatching the event.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.fire('customEvent', { key: 'value' }); // Dispatches a custom event with data
     */
    fire(eventName, detail, options, callback) {
      this.beElement.eachNode((el) => {
        el.dispatchEvent(new CustomEvent(eventName, __spreadProps(__spreadValues({}, options), { detail })));
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    valueOf() {
      return this.beElement;
    }
  };
  __publicField(_EventsHandler, "methods", Object.values(eventsMethods));
  var EventsHandler = _EventsHandler;

  // ../../../../idae/packages/idae-be/dist/modules/classes.js
  var classesMethods;
  (function(classesMethods2) {
    classesMethods2["add"] = "add";
    classesMethods2["remove"] = "remove";
    classesMethods2["toggle"] = "toggle";
    classesMethods2["replace"] = "replace";
  })(classesMethods || (classesMethods = {}));
  var _ClassesHandler = class _ClassesHandler {
    constructor(beElement) {
      __publicField(this, "beElement");
      __publicField(this, "methods", _ClassesHandler.methods);
      this.beElement = beElement;
    }
    valueOf() {
      return `[ClassesHandler: methods=${this.methods}]`;
    }
    handle(actions) {
      if (!actions)
        return this.beElement;
      Object.entries(actions).forEach(([method, props]) => {
        switch (method) {
          default:
            this[method](props);
            break;
        }
      });
      return this.beElement;
    }
    /**
     * Adds one or more classes to the element(s).
     * @param className - The class or classes to add.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.addClass('highlight'); // Adds a single class
     * beInstance.addClass(['highlight', 'active']); // Adds multiple classes
     */
    add(className) {
      const classesToAdd = Array.isArray(className) ? className : className.split(" ");
      this.beElement.eachNode((el) => el.classList.add(...classesToAdd.filter((c) => c.trim() !== "")));
      return this.beElement;
    }
    /**
     * Toggles a class on the element(s).
     * @param className - The class or classes to toggle.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test" class="highlight"></div>
     * const beInstance = be('#test');
     * beInstance.toggleClass('highlight'); // Removes the "highlight" class
     * beInstance.toggleClass('active'); // Adds the "active" class
     */
    toggle(className) {
      const classesToToggle = Array.isArray(className) ? className : className.split(" ");
      classesToToggle.forEach((className2) => {
        this.beElement.eachNode((el) => el.classList.toggle(className2));
      });
      return this.beElement;
    }
    /**
     * Replaces a class on the element(s) with another class.
     * @param sourceClassName - The class to replace.
     * @param targetClassName - The class to replace it with.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test" class="old-class"></div>
     * const beInstance = be('#test');
     * beInstance.replaceClass('old-class', 'new-class'); // Replaces "old-class" with "new-class"
     */
    replace(sourceClassName, targetClassName) {
      this.beElement.eachNode((el) => el.classList.replace(sourceClassName, targetClassName));
      return this.beElement;
    }
    /**
     * Removes one or more classes from the element(s).
     * @param className - The class or classes to remove.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test" class="highlight active"></div>
     * const beInstance = be('#test');
     * beInstance.removeClass('highlight'); // Removes the "highlight" class
     * beInstance.removeClass(['highlight', 'active']); // Removes multiple classes
     */
    remove(className) {
      const classesToRemove = Array.isArray(className) ? className : className.split(" ");
      this.beElement.eachNode((el) => el.classList.remove(...classesToRemove.filter((c) => c.trim() !== "")));
      return this.beElement;
    }
  };
  __publicField(_ClassesHandler, "methods", Object.values(classesMethods));
  var ClassesHandler = _ClassesHandler;

  // ../../../../idae/packages/idae-be/dist/modules/dom.js
  var domMethods;
  (function(domMethods2) {
    domMethods2["update"] = "update";
    domMethods2["append"] = "append";
    domMethods2["prepend"] = "prepend";
    domMethods2["insert"] = "insert";
    domMethods2["afterBegin"] = "afterBegin";
    domMethods2["afterEnd"] = "afterEnd";
    domMethods2["beforeBegin"] = "beforeBegin";
    domMethods2["beforeEnd"] = "beforeEnd";
    domMethods2["remove"] = "remove";
    domMethods2["wrap"] = "wrap";
    domMethods2["normalize"] = "normalize";
    domMethods2["replace"] = "replace";
    domMethods2["clear"] = "clear";
    domMethods2["unwrap"] = "unwrap";
  })(domMethods || (domMethods = {}));
  var _DomHandler = class _DomHandler {
    constructor(element) {
      __publicField(this, "beElement");
      __publicField(this, "methods", _DomHandler.methods);
      this.beElement = element;
    }
    /**
     * Handles various DOM operations on the element(s).
     * @param actions An object specifying the DOM actions to perform.
     * @param actions.update - HTML content to update the element(s) with.
     * @param actions.append - Content to append to the element(s).
     * @param actions.prepend - Content to prepend to the element(s).
     * @param actions.remove - If true, removes the element(s) from the DOM.
     * @param actions.replace - Content to replace the element(s) with.
     * @param actions.clear - If true, clears the content of the element(s).
     * @returns The Be instance for method chaining.
     */
    handle(actions) {
      Object.entries(actions).forEach(([method, props]) => {
        switch (method) {
          case "update":
            this.update(props.content, props.callback);
            break;
          case "append":
            this.append(props.content, props.callback);
            break;
          case "prepend":
            this.prepend(props.content, props.callback);
            break;
          case "replace":
            this.replace(props.content, props.callback);
            break;
          case "remove":
            this.remove(props.callback);
            break;
          case "clear":
            this.clear(props.callback);
            break;
          case "normalize":
            this.normalize(props.callback);
            break;
          case "wrap":
            this.wrap(props.tag, props.callback);
            break;
          case "unwrap":
            this.unwrap(props.callback);
            break;
        }
      });
      return this.beElement;
    }
    /**
     * Updates the content of the element(s).
     * @param content - The new content to set.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.update('<p>Updated content</p>'); // Updates the content of the element
     */
    update(content, callback) {
      this.beElement.eachNode((el) => {
        if (el) {
          el.innerHTML = content;
          callback == null ? void 0 : callback({
            fragment: content,
            be: be(el),
            root: this.beElement
          });
        }
      });
      return this.beElement;
    }
    /**
     * Appends content to the element(s).
     * @param content - The content to append (string, HTMLElement, or Be instance).
     * @param callback - Optional callback function to execute after appending.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.append('<span>Appended</span>'); // Appends content to the element
     */
    append(content, callback) {
      const ret = [];
      this.beElement.eachNode((el) => {
        const normalizedContent = this.normalizeContent(content);
        if (normalizedContent instanceof DocumentFragment) {
          el.appendChild(normalizedContent);
        } else {
          ret.push(normalizedContent);
          el.appendChild(normalizedContent);
        }
      });
      callback == null ? void 0 : callback({
        fragment: content,
        be: be(ret),
        root: this.beElement
      });
      return this.beElement;
    }
    /**
     * Prepends content to the element(s).
     * @param content - The content to prepend (string, HTMLElement, or Be instance).
     * @param callback - Optional callback function to execute after prepending.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.prepend('<span>Prepended</span>'); // Prepends content to the element
     */
    prepend(content, callback) {
      const ret = [];
      this.beElement.eachNode((el) => {
        const normalizedContent = this.normalizeContent(content);
        if (normalizedContent instanceof DocumentFragment) {
          el.insertBefore(normalizedContent, el.firstChild);
        } else {
          ret.push(normalizedContent);
          el.insertBefore(normalizedContent, el.firstChild);
        }
      });
      callback == null ? void 0 : callback({
        fragment: content,
        be: be(ret),
        root: this.beElement
      });
      return this.beElement;
    }
    /**
     * Inserts content into the element(s) at a specified position.
     * @param mode - The position to insert the content ('afterbegin', 'afterend', 'beforebegin', 'beforeend').
     * @param element - The content to insert (string, HTMLElement, or Be instance).
     * @param callback - Optional callback function to execute after insertion.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.insert('afterbegin', '<span>Inserted</span>'); // Inserts content at the beginning
     */
    insert(mode, element, callback) {
      switch (mode) {
        case "afterbegin":
          return this.afterBegin(element, callback);
        case "afterend":
          return this.afterEnd(element, callback);
        case "beforebegin":
          return this.beforeBegin(element, callback);
        case "beforeend":
          return this.beforeEnd(element, callback);
        default:
          throw new Error(`Invalid mode: ${mode}`);
      }
    }
    /**
     * Inserts content at the beginning of the element(s).
     * @param content - The content to insert (string, HTMLElement, or Be instance).
     * @param callback - Optional callback function to execute after insertion.
     * @returns The Be instance for method chaining.
     */
    afterBegin(content, callback) {
      this.beElement.eachNode((el) => {
        this.adjacentElement(el, content, "afterbegin");
        callback == null ? void 0 : callback({
          fragment: content,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Inserts content after the element(s).
     * @param content - The content to insert (string, HTMLElement, or Be instance).
     * @param callback - Optional callback function to execute after insertion.
     * @returns The Be instance for method chaining.
     */
    afterEnd(content, callback) {
      this.beElement.eachNode((el) => {
        var _a;
        (_a = el.parentNode) == null ? void 0 : _a.insertBefore(this.normalizeContent(content), el.nextSibling);
        callback == null ? void 0 : callback({
          fragment: content,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Inserts content before the element(s).
     * @param content - The content to insert (string, HTMLElement, or Be instance).
     * @param callback - Optional callback function to execute after insertion.
     * @returns The Be instance for method chaining.
     */
    beforeBegin(content, callback) {
      this.beElement.eachNode((el) => {
        var _a;
        (_a = el.parentNode) == null ? void 0 : _a.insertBefore(this.normalizeContent(content), el);
        callback == null ? void 0 : callback({
          fragment: content,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Inserts content at the end of the element(s).
     * @param content - The content to insert (string, HTMLElement, or Be instance).
     * @param callback - Optional callback function to execute after insertion.
     * @returns The Be instance for method chaining.
     */
    beforeEnd(content, callback) {
      this.beElement.eachNode((el) => {
        el.appendChild(this.normalizeContent(content));
        callback == null ? void 0 : callback({
          fragment: content,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Replaces the element(s) with new content.
     * @param content - The content to replace the element(s) with (string, HTMLElement, or Be instance).
     * @param callback - Optional callback function to execute after replacement.
     * @returns The Be instance for method chaining.
     */
    replace(content, callback) {
      const ret = [];
      this.beElement.eachNode((el) => {
        const normalizedContent = this.normalizeContent(content);
        if (normalizedContent instanceof DocumentFragment) {
          el.replaceWith(...normalizedContent.childNodes);
        } else {
          ret.push(normalizedContent);
          el.replaceWith(normalizedContent);
        }
      });
      callback == null ? void 0 : callback({
        fragment: content,
        be: be(ret),
        root: this.beElement
      });
      return this.beElement;
    }
    /**
     * Removes the element(s) from the DOM.
     * @param callback - Optional callback function to execute after removal.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"><span>To be removed</span></div>
     * const beInstance = be('#test span');
     * beInstance.remove(); // Removes the span element
     */
    remove(callback) {
      this.beElement.eachNode((el) => {
        el.remove();
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Clears the content of the element(s).
     * @param callback - Optional callback function to execute after clearing.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"><span>Content</span></div>
     * const beInstance = be('#test');
     * beInstance.clear(); // Clears the content of the div
     */
    clear(callback) {
      this.beElement.eachNode((el) => {
        const fragment = el.innerHTML;
        el.innerHTML = "";
        callback == null ? void 0 : callback({
          fragment,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Normalizes the content of the element(s).
     * @param callback - Optional callback function to execute after normalization.
     * @returns The Be instance for method chaining.
     */
    normalize(callback) {
      this.beElement.eachNode((el) => {
        el.normalize();
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Wraps the element(s) with a new element.
     * @param tag - The tag name of the wrapper element (default is 'div').
     * @param callback - Optional callback function to execute after wrapping.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.wrap('section'); // Wraps the div with a <section> element
     */
    wrap(tag = "div", callback) {
      this.beElement.eachNode((el) => {
        const wrapper = document.createElement(tag);
        el.insertAdjacentElement("beforebegin", wrapper);
        wrapper.appendChild(el);
        callback == null ? void 0 : callback({
          fragment: tag,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Removes the parent element of the selected element(s), keeping the selected element(s) in the DOM.
     * @param callback - Optional callback function to execute after unwrapping.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="wrapper"><span id="child">Content</span></div>
     * const beInstance = be('#child');
     * beInstance.unwrap(); // Removes the <div id="wrapper">, keeping <span id="child">
     */
    unwrap(callback) {
      this.beElement.eachNode((el) => {
        const parent = el.parentElement;
        if (parent) {
          while (el.firstChild) {
            parent.insertBefore(el.firstChild, el);
          }
          parent.replaceWith(...Array.from(parent.childNodes));
        }
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    adjacentElement(element, content, mode) {
      var _a, _b;
      const normalizedContent = this.normalizeContent(content);
      if (typeof content === "string") {
        element.insertAdjacentHTML(mode, content);
      } else if (normalizedContent instanceof HTMLElement) {
        if (mode === "afterend") {
          (_a = element.parentNode) == null ? void 0 : _a.insertBefore(normalizedContent, element.nextSibling);
        } else if (mode === "beforebegin") {
          (_b = element.parentNode) == null ? void 0 : _b.insertBefore(normalizedContent, element);
        } else {
          element.insertAdjacentElement(mode, normalizedContent);
        }
      } else if (normalizedContent instanceof DocumentFragment) {
        Array.from(normalizedContent.childNodes).forEach((node) => {
          var _a2, _b2;
          if (mode === "afterbegin" || mode === "beforeend") {
            element.appendChild(node);
          } else if (mode === "afterend") {
            (_a2 = element.parentNode) == null ? void 0 : _a2.insertBefore(node, element.nextSibling);
          } else if (mode === "beforebegin") {
            (_b2 = element.parentNode) == null ? void 0 : _b2.insertBefore(node, element);
          }
        });
      }
    }
    normalizeContent(content) {
      if (typeof content === "string") {
        const template = document.createElement("template");
        template.innerHTML = content.trim();
        return template.content;
      } else if (content instanceof Be) {
        if (Array.isArray(content.node)) {
          const fragment = document.createDocumentFragment();
          content.node.forEach((node) => {
            if (node instanceof HTMLElement) {
              fragment.appendChild(node);
            }
          });
          return fragment;
        } else if (content.node instanceof HTMLElement) {
          return content.node;
        }
        throw new Error("Invalid Be instance: no valid node found.");
      } else {
        return content;
      }
    }
    insertContent(el, content, mode) {
      var _a;
      const normalizedContent = this.normalizeContent(content);
      if (mode === "afterEnd") {
        (_a = el.parentNode) == null ? void 0 : _a.insertBefore(normalizedContent, el.nextSibling);
      } else if (mode === "beforeEnd") {
        el.appendChild(normalizedContent);
      }
    }
    valueOf() {
      if (this.beElement.isWhat !== "element")
        return null;
      return this.beElement.inputNode.innerHTML;
    }
  };
  __publicField(_DomHandler, "methods", Object.values(domMethods));
  var DomHandler = _DomHandler;

  // ../../../../idae/packages/idae-be/dist/modules/position.js
  var positionMethods;
  (function(positionMethods2) {
    positionMethods2["clonePosition"] = "clonePosition";
    positionMethods2["overlapPosition"] = "overlapPosition";
    positionMethods2["snapTo"] = "snapTo";
  })(positionMethods || (positionMethods = {}));
  var _PositionHandler = class _PositionHandler {
    constructor(beElement) {
      __publicField(this, "beElement");
      __publicField(this, "methods", _PositionHandler.methods);
      this.beElement = beElement;
    }
    handle(actions) {
      Object.entries(actions).forEach(([method, props]) => {
        switch (method) {
          case "clonePosition":
            this.clonePosition(props.source, props.options, props.callback);
            break;
          case "overlapPosition":
            this.overlapPosition(props.source, props.options, props.callback);
            break;
          case "snapTo":
            this.snapTo(props.target, props.options, props.callback);
            break;
        }
      });
      return this.beElement;
    }
    /**
     * Clones the position of a source element to this element.
     * @param source - The element or selector of the element whose position is to be cloned.
     * @param options - Additional options for positioning.
     * @param options.offsetX - Horizontal offset from the source position.
     * @param options.offsetY - Vertical offset from the source position.
     * @param options.useTransform - Whether to use CSS transform for positioning.
     * @param callback - Optional callback function to execute after cloning the position.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="source"></div><div id="target"></div>
     * const beInstance = be('#target');
     * beInstance.clonePosition('#source', { offsetX: 10, offsetY: 20 });
     */
    clonePosition(source, options = {}, callback) {
      if (this.beElement.isWhat !== "element")
        return this.beElement;
      const sourceEl = typeof source === "string" ? document.querySelector(source) : source;
      if (!sourceEl)
        return this.beElement;
      const sourceRect = sourceEl.getBoundingClientRect();
      const targetRect = this.beElement.inputNode.getBoundingClientRect();
      const { offsetX = 0, offsetY = 0, useTransform = false } = options;
      this.beElement.eachNode((el) => {
        if (useTransform) {
          const x = sourceRect.left - targetRect.left + offsetX;
          const y = sourceRect.top - targetRect.top + offsetY;
          el.style.transform = `translate(${x}px, ${y}px)`;
        } else {
          el.style.left = `${sourceRect.left + offsetX}px`;
          el.style.top = `${sourceRect.top + offsetY}px`;
        }
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Positions this element to overlap a target element.
     * @param targetElement - The element or selector of the element to overlap.
     * @param options - Additional options for positioning.
     * @param options.alignment - The alignment of this element relative to the target.
     * @param options.offset - The distance to offset from the target element.
     * @param options.useTransform - Whether to use CSS transform for positioning.
     * @param callback - Optional callback function to execute after positioning.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="source"></div><div id="target"></div>
     * const beInstance = be('#target');
     * beInstance.overlapPosition('#source', { alignment: 'center', offset: 10 });
     */
    overlapPosition(targetElement, options = {}, callback) {
      if (this.beElement.isWhat !== "element")
        return this.beElement;
      const targetEl = typeof targetElement === "string" ? document.querySelector(targetElement) : targetElement;
      if (!targetEl)
        return this.beElement;
      const { alignment = "center", offset = 0, useTransform = false } = options;
      const targetRect = targetEl.getBoundingClientRect();
      const selfRect = this.beElement.inputNode.getBoundingClientRect();
      let x = 0, y = 0;
      switch (alignment) {
        case "center":
          x = targetRect.left + (targetRect.width - selfRect.width) / 2;
          y = targetRect.top + (targetRect.height - selfRect.height) / 2;
          break;
        case "top":
          x = targetRect.left + (targetRect.width - selfRect.width) / 2;
          y = targetRect.top - selfRect.height - offset;
          break;
        case "bottom":
          x = targetRect.left + (targetRect.width - selfRect.width) / 2;
          y = targetRect.bottom + offset;
          break;
        case "left":
          x = targetRect.left - selfRect.width - offset;
          y = targetRect.top + (targetRect.height - selfRect.height) / 2;
          break;
        case "right":
          x = targetRect.right + offset;
          y = targetRect.top + (targetRect.height - selfRect.height) / 2;
          break;
      }
      this.beElement.eachNode((el) => {
        if (useTransform) {
          el.style.transform = `translate(${x}px, ${y}px)`;
        } else {
          el.style.left = `${x}px`;
          el.style.top = `${y}px`;
        }
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Snaps the element to a target element with specified anchor points.
     * @param targetElement - The element or selector of the element to snap to.
     * @param options - Snapping options.
     * @param options.sourceAnchor - The anchor point on the source element.
     * @param options.targetAnchor - The anchor point on the target element.
     * @param options.offset - Optional offset from the target anchor point.
     * @param callback - Optional callback function to execute after snapping.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="source"></div><div id="target"></div>
     * const beInstance = be('#target');
     * beInstance.snapTo('#source', {
     *   sourceAnchor: 'center',
     *   targetAnchor: 'top left',
     *   offset: { x: 10, y: 20 }
     * });
     */
    snapTo(targetElement, options, callback) {
      if (this.beElement.isWhat !== "element")
        return this.beElement;
      const targetEl = typeof targetElement === "string" ? document.querySelector(targetElement) : targetElement;
      if (!targetEl)
        return this.beElement;
      const sourceRect = this.beElement.inputNode.getBoundingClientRect();
      const targetRect = targetEl.getBoundingClientRect();
      const { sourceAnchor, targetAnchor, offset = { x: 0, y: 0 } } = options;
      const [sourceX, sourceY] = BeUtils.calculateAnchorPoint(sourceRect, sourceAnchor);
      const [targetX, targetY] = BeUtils.calculateAnchorPoint(targetRect, targetAnchor);
      const x = targetX - sourceX + offset.x;
      const y = targetY - sourceY + offset.y;
      this.beElement.eachNode((el) => {
        const computedStyle = window.getComputedStyle(el);
        const position = computedStyle.position;
        if (position === "static") {
          el.style.position = "relative";
        }
        el.style.left = `${x}px`;
        el.style.top = `${y}px`;
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    valueOf() {
      if (this.beElement.isWhat !== "element")
        return null;
      return this.beElement.inputNode.getBoundingClientRect();
    }
  };
  __publicField(_PositionHandler, "methods", Object.values(positionMethods));
  var PositionHandler = _PositionHandler;

  // ../../../../idae/packages/idae-be/dist/modules/walk.js
  var walkerMethods;
  (function(walkerMethods2) {
    walkerMethods2["up"] = "up";
    walkerMethods2["next"] = "next";
    walkerMethods2["previous"] = "previous";
    walkerMethods2["siblings"] = "siblings";
    walkerMethods2["children"] = "children";
    walkerMethods2["closest"] = "closest";
    walkerMethods2["lastChild"] = "lastChild";
    walkerMethods2["firstChild"] = "firstChild";
    walkerMethods2["find"] = "find";
    walkerMethods2["findAll"] = "findAll";
    walkerMethods2["without"] = "without";
  })(walkerMethods || (walkerMethods = {}));
  var _WalkHandler = class _WalkHandler {
    /**
     * Creates an instance of WalkHandler.
     * @param beElement - The Be element to operate on.
     */
    constructor(beElement) {
      __publicField(this, "beElement");
      __publicField(this, "methods", _WalkHandler.methods);
      this.beElement = beElement;
    }
    valueOf() {
      return this.beElement;
    }
    /**
     * Handles multiple walk operations.
     * @param actions - The actions to perform.
     * @returns The Be instance for method chaining.
     */
    handle(actions) {
      Object.entries(actions).forEach(([method, props]) => {
        if (method in this) {
          this[method](props);
        }
      });
      return this.beElement;
    }
    /**
     * Traverses up the DOM tree.
     * @param qy - Optional selector or callback function.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="child"></div><div id="parent"><div id="child"></div></div>
     * const beInstance = be('#child');
     * beInstance.up(); // Traverses to the parent element
     */
    up(qy, callback) {
      if (typeof qy === "function") {
        callback = qy;
        qy = void 0;
      }
      return this.methodize("up")(qy, callback);
    }
    /**
     * Traverses to the next sibling element.
     * @param qy - Optional selector or callback function.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="sibling1"></div><div id="sibling2"></div>
     * const beInstance = be('#sibling1');
     * beInstance.next(); // Traverses to the next sibling
     */
    next(qy, callback) {
      if (typeof qy === "function") {
        callback = qy;
        qy = void 0;
      }
      return this.methodize("next")(qy, callback);
    }
    /**
     * Traverses to the previous sibling element.
     * @param qy - Optional selector or callback function.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="sibling1"></div><div id="sibling2"></div>
     * const beInstance = be('#sibling2');
     * beInstance.previous(); // Traverses to the previous sibling
     */
    previous(qy, callback) {
      if (typeof qy === "function") {
        callback = qy;
        qy = void 0;
      }
      return this.methodize("previous")(qy, callback);
    }
    /**
     * Filters out elements that match the given selector.
     * @param qy - The selector to match elements against for removal.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     */
    without(qy, callback) {
      const ret = [];
      this.beElement.eachNode((el) => {
        if (!el.matches(qy)) {
          ret.push(el);
        }
      });
      const resultBe = Be.elem(ret);
      callback == null ? void 0 : callback({
        root: this.beElement,
        be: resultBe,
        fragment: "result",
        requested: resultBe
      });
      return this.beElement;
    }
    /**
     * Gets all sibling elements.
     * @param qy - Optional selector or callback function.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="sibling1"></div><div id="sibling2"></div>
     * const beInstance = be('#sibling1');
     * beInstance.siblings(); // Finds all sibling elements
     */
    siblings(qy, callback) {
      if (typeof qy === "function") {
        callback = qy;
        qy = void 0;
      }
      const ret = [];
      this.beElement.eachNode((el) => {
        if (el.parentNode) {
          const siblings = Array.from(el.parentNode.children).filter((child) => child !== el);
          ret.push(...siblings.filter((sibling) => !qy || sibling.matches(qy)));
        }
      });
      callback == null ? void 0 : callback({
        root: this.beElement,
        be: Be.elem(ret),
        fragment: "result",
        requested: Be.elem(ret)
      });
      return this.beElement;
    }
    /**
     * Gets all child elements.
     * @param qy - Optional selector or callback function.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="parent"><div id="child"></div></div>
     * const beInstance = be('#parent');
     * beInstance.children(); // Finds all child elements
     */
    children(qy, callback) {
      if (typeof qy === "function") {
        callback = qy;
        qy = void 0;
      }
      return this.methodize("children")(qy, callback);
    }
    /**
     * Finds the closest ancestor that matches the selector.
     * @param qy - Optional selector or callback function.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="ancestor"><div id="parent"><div id="child"></div></div></div>
     * const beInstance = be('#child');
     * beInstance.closest('#ancestor'); // Finds the closest ancestor matching the selector
     */
    closest(qy, callback) {
      if (typeof qy === "function") {
        callback = qy;
        qy = void 0;
      }
      return this.methodize("closest")(qy, callback);
    }
    /**
     * Gets the last child element.
     * @param qy - Optional selector or callback function.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     */
    lastChild(qy, callback) {
      if (typeof qy === "function") {
        callback = qy;
        qy = void 0;
      }
      return this.methodize("lastChild")(qy, callback);
    }
    /**
     * Gets the first child element.
     * @param qy - Optional selector or callback function.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     */
    firstChild(qy, callback) {
      if (typeof qy === "function") {
        callback = qy;
        qy = void 0;
      }
      return this.methodize("firstChild")(qy, callback);
    }
    /**
     * Finds the first descendant that matches the selector.
     * @param qy - The selector to match against.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     */
    find(qy, callback) {
      const ret = [];
      this.beElement.eachNode((el) => {
        const found = el.querySelector(qy);
        if (found)
          ret.push(found);
      });
      const resultBe = Be.elem(ret);
      callback == null ? void 0 : callback({
        root: this.beElement,
        be: resultBe,
        fragment: "result",
        requested: resultBe
      });
      return resultBe;
    }
    /**
     * Finds all descendants that match the selector.
     * @param qy - The selector to match against.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     */
    findAll(qy, callback) {
      const ret = [];
      this.beElement.eachNode((el) => {
        ret.push(...Array.from(el.querySelectorAll(qy)));
      });
      callback == null ? void 0 : callback({
        root: this.beElement,
        be: Be.elem(ret),
        fragment: "result",
        requested: Be.elem(ret)
      });
      return this.beElement;
    }
    /**
     * Helper method to create a function for each walk method.
     * @param method - The walk method to create a function for.
     * @returns A function that performs the specified walk method.
     */
    methodize(method) {
      return (qy, callback) => {
        try {
          const ret = [];
          this.beElement.eachNode((el) => {
            const result = this.selectWhile(el, method, qy);
            if (result)
              ret.push(...Array.isArray(result) ? result : [result]);
          });
          const resultBe = Be.elem(ret);
          callback == null ? void 0 : callback({
            root: this.beElement,
            be: resultBe,
            fragment: "result",
            requested: resultBe
          });
          return resultBe;
        } catch (e) {
          console.error(`Error in methodize for ${method}:`, e);
        }
        return this.beElement;
      };
    }
    /**
     * Helper method to select elements based on the specified method and selector.
     * @param element - The starting element.
     * @param direction - The direction to traverse.
     * @param selector - Optional selector to filter elements.
     * @returns The selected HTMLElement or null if not found.
     */
    selectWhile(element, direction, selector) {
      const dict = {
        up: "parentElement",
        next: "nextElementSibling",
        previous: "previousElementSibling",
        siblings: "parentElement",
        children: "children",
        firstChild: "firstElementChild",
        lastChild: "lastElementChild",
        closest: "closest"
      };
      const property = dict[direction];
      if (direction === "up") {
        let current = element;
        while (current) {
          current = current[property];
          if (!selector || current && current.matches(selector)) {
            return current;
          }
        }
        return null;
      }
      if (direction === "siblings") {
        const parent = element.parentElement;
        if (!parent)
          return [];
        const siblings = Array.from(parent.children).filter((child) => child !== element);
        return selector ? siblings.filter((sibling) => sibling.matches(selector)) : siblings;
      }
      if (direction === "children") {
        const children = Array.from(element.children);
        return selector ? children.filter((child) => child.matches(selector)) : children;
      }
      if (direction === "closest") {
        const closest = element.closest(selector != null ? selector : "*");
        return closest;
      }
      const target = element[property];
      return target && (!selector || target.matches(selector)) ? target : null;
    }
  };
  __publicField(_WalkHandler, "methods", Object.values(walkerMethods));
  var WalkHandler = _WalkHandler;

  // ../../../../idae/packages/idae-be/dist/modules/text.js
  var textMethods;
  (function(textMethods2) {
    textMethods2["update"] = "update";
    textMethods2["append"] = "append";
    textMethods2["prepend"] = "prepend";
    textMethods2["remove"] = "remove";
    textMethods2["wrap"] = "wrap";
    textMethods2["normalize"] = "normalize";
    textMethods2["replace"] = "replace";
    textMethods2["clear"] = "clear";
  })(textMethods || (textMethods = {}));
  var _TextHandler = class _TextHandler {
    constructor(element) {
      __publicField(this, "beElement");
      __publicField(this, "methods", _TextHandler.methods);
      this.beElement = element;
    }
    get text() {
      if (this.beElement.isWhat !== "element")
        return null;
      return this.beElement.inputNode.textContent;
    }
    handle(actions) {
      Object.entries(actions).forEach(([method, props]) => {
        if (method in this) {
          this[method](props);
        }
      });
      return this.beElement;
    }
    /**
     * Updates the text content of the element(s).
     * @param content - The new text content to set.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test">Original</div>
     * const beInstance = be('#test');
     * beInstance.updateText('Updated'); // Updates the text content to "Updated"
     */
    update(content, callback) {
      this.beElement.eachNode((el) => {
        if (typeof content === "string") {
          el.innerText = content;
        }
        callback == null ? void 0 : callback({
          fragment: content,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Appends text content to the element(s).
     * @param content - The text content to append.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test">Original</div>
     * const beInstance = be('#test');
     * beInstance.appendText(' Appended'); // Appends " Appended" to the text content
     */
    append(content, callback) {
      this.beElement.eachNode((el) => {
        if (typeof content === "string") {
          el.insertAdjacentText("beforeend", content);
        }
        callback == null ? void 0 : callback({
          fragment: content,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Prepends text content to the element(s).
     * @param content - The text content to prepend.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test">Original</div>
     * const beInstance = be('#test');
     * beInstance.prependText('Prepended '); // Prepends "Prepended " to the text content
     */
    prepend(content, callback) {
      this.beElement.eachNode((el) => {
        if (typeof content === "string") {
          el.insertAdjacentText("afterbegin", content);
        }
        callback == null ? void 0 : callback({
          fragment: content,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Replaces the text content of the element(s).
     * @param content - The new text content to replace with.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test">Original</div>
     * const beInstance = be('#test');
     * beInstance.replaceText('Replaced'); // Replaces the text content with "Replaced"
     */
    replace(content, callback) {
      this.beElement.eachNode((el) => {
        if (typeof content === "string") {
          el.textContent = content;
        }
        callback == null ? void 0 : callback({
          fragment: content,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Removes the element(s) from the DOM.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test">To be removed</div>
     * const beInstance = be('#test');
     * beInstance.removeText(); // Removes the element
     */
    remove(callback) {
      this.beElement.eachNode((el) => {
        el.remove();
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Clears the text content of the element(s).
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test">Content</div>
     * const beInstance = be('#test');
     * beInstance.clearText(); // Clears the text content
     */
    clear(callback) {
      this.beElement.eachNode((el) => {
        el.innerHTML = "";
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Normalizes the text content of the element(s).
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test">Text <span>Fragment</span> Text</div>
     * const beInstance = be('#test');
     * beInstance.normalizeText(); // Normalizes the text content
     */
    normalize(callback) {
      this.beElement.eachNode((el) => {
        el.normalize();
        callback == null ? void 0 : callback({
          fragment: void 0,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    /**
     * Wraps the element(s) with a new element.
     * @param content - The wrapper element as a string or HTMLElement.
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test">Content</div>
     * const beInstance = be('#test');
     * beInstance.wrapText('<div class="wrapper"></div>'); // Wraps the element with a <div>
     */
    wrap(content, callback) {
      this.beElement.eachNode((el) => {
        var _a;
        if (typeof content === "string") {
          const wrapper = document.createElement("div");
          wrapper.innerHTML = content.trim();
          const parent = wrapper.firstElementChild;
          if (parent) {
            (_a = el.parentNode) == null ? void 0 : _a.insertBefore(parent, el);
            parent.appendChild(el);
          }
        }
        callback == null ? void 0 : callback({
          fragment: content,
          be: be(el),
          root: this.beElement
        });
      });
      return this.beElement;
    }
    valueOf() {
      if (this.beElement.isWhat !== "element")
        return null;
      return this.beElement.inputNode.innerText;
    }
  };
  __publicField(_TextHandler, "methods", Object.values(textMethods));
  var TextHandler = _TextHandler;

  // ../../../../idae/packages/idae-be/dist/modules/timers.js
  var timersMethods;
  (function(timersMethods2) {
    timersMethods2["timeout"] = "timeout";
    timersMethods2["interval"] = "interval";
    timersMethods2["clearTimeout"] = "clearTimeout";
    timersMethods2["clearInterval"] = "clearInterval";
  })(timersMethods || (timersMethods = {}));
  var _timer, _interval;
  var _TimersHandler = class _TimersHandler {
    constructor(element) {
      __publicField(this, "beElement");
      __privateAdd(this, _timer, null);
      __privateAdd(this, _interval, null);
      __publicField(this, "methods", _TimersHandler.methods);
      this.beElement = element;
    }
    valueOf() {
      return {
        methods: this.methods,
        timer: __privateGet(this, _timer),
        interval: __privateGet(this, _interval)
      };
    }
    handle(actions) {
      Object.entries(actions).forEach(([method, props]) => {
        if (method in this) {
          this[method](props);
        }
      });
      return this.beElement;
    }
    /**
     * Sets a timeout for the element(s).
     * @param value - The timeout duration in milliseconds.
     * @param callback - The callback function to execute after the timeout.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.timeout(1000, () => console.log('Timeout executed')); // Sets a 1-second timeout
     */
    timeout(value, callback) {
      this.beElement.timerOut = setTimeout(() => {
        callback == null ? void 0 : callback({
          method: "timeout",
          fragment: this.beElement.timerOut,
          be: this.beElement,
          root: this.beElement
        });
      }, value);
      return this.beElement;
    }
    /**
     * Sets an interval for the element(s).
     * @param value - The interval duration in milliseconds.
     * @param callback - The callback function to execute at each interval.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.interval(500, () => console.log('Interval executed')); // Sets a 500ms interval
     */
    interval(value, callback) {
      this.beElement.timerInterval = setInterval(() => {
        callback == null ? void 0 : callback({
          method: "interval",
          fragment: this.beElement.timerInterval,
          be: this.beElement,
          root: this.beElement
        });
      }, value);
      return this.beElement;
    }
    /**
     * Clears the timeout for the element(s).
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.timeout(1000, () => console.log('Timeout executed'));
     * beInstance.clearTimeout(); // Clears the timeout
     */
    clearTimeout(callback) {
      if (this.beElement.timerOut)
        clearTimeout(this.beElement.timerOut);
      this.beElement.timerOut = null;
      callback == null ? void 0 : callback({
        method: "clearTimeout",
        fragment: null,
        be: this.beElement,
        root: this.beElement
      });
      return this.beElement;
    }
    /**
     * Clears the interval for the element(s).
     * @param callback - Optional callback function.
     * @returns The Be instance for method chaining.
     * @example
     * // HTML: <div id="test"></div>
     * const beInstance = be('#test');
     * beInstance.interval(500, () => console.log('Interval executed'));
     * beInstance.clearInterval(); // Clears the interval
     */
    clearInterval(callback) {
      if (this.beElement.timerInterval)
        clearInterval(this.beElement.timerInterval);
      this.beElement.timerInterval = null;
      callback == null ? void 0 : callback({
        method: "clearInterval",
        fragment: null,
        be: this.beElement,
        root: this.beElement
      });
      return this.beElement;
    }
  };
  _timer = new WeakMap();
  _interval = new WeakMap();
  __publicField(_TimersHandler, "methods", Object.values(timersMethods));
  var TimersHandler = _TimersHandler;

  // ../../../../idae/packages/idae-be/dist/modules/http.js
  var httpMethods;
  (function(httpMethods2) {
    httpMethods2["update"] = "update";
    httpMethods2["insert"] = "insert";
  })(httpMethods || (httpMethods = {}));
  var _HttpHandler = class _HttpHandler {
    constructor(beElement) {
      __publicField(this, "beElement");
      __publicField(this, "methods", _HttpHandler.methods);
      this.beElement = beElement;
    }
    /**
     * Handles HTTP actions like loading content or inserting it into the DOM.
     * @param actions - The actions to perform.
     * @returns The Be instance for method chaining.
     */
    handle(actions) {
      Object.entries(actions).forEach(([method, props]) => {
        switch (method) {
          case "update":
            this.update(props.url, props.options, props.callback);
            break;
          case "insert":
            this.insert(props.url, props.mode, props.callback);
            break;
        }
      });
      return this.beElement;
    }
    /**
     * Loads content from a URL and updates the element's content.
     * Can be called with two or three arguments:
     * - `update(url: string, callback?: HandlerCallBackFn)`
     * - `update(url: string, options?: { method?: string; data?: object; headers?: object }, callback?: HandlerCallBackFn)`
     *
     * @param url - The URL to fetch content from.
     * @param optionsOrCallback - Optional configuration for the HTTP request or a callback function.
     * @param callback - Optional callback function if options are provided.
     * @returns The Be instance for method chaining.
     * @example
     * // Call with two arguments
     * be('#test').updateHttp('/content.html', ({ be }) => console.log(be.html));
     *
     * // Call with three arguments
     * be('#test').updateHttp('/content.html', { method: 'POST', data: { key: 'value' } }, ({ be }) => console.log(be.html));
     */
    async update(url, optionsOrCallback, callback) {
      let options;
      if (typeof optionsOrCallback === "function") {
        callback = optionsOrCallback;
      } else {
        options = optionsOrCallback;
      }
      const response = await fetch(url, {
        method: (options == null ? void 0 : options.method) || "GET",
        body: (options == null ? void 0 : options.data) ? JSON.stringify(options.data) : void 0,
        headers: __spreadValues({
          "Content-Type": "application/json"
        }, options == null ? void 0 : options.headers)
      });
      const content = await response.text();
      this.beElement.eachNode((el) => {
        const beElem = Be.elem(el);
        beElem.update(content);
        callback == null ? void 0 : callback({
          fragment: content,
          be: beElem,
          root: this.beElement
        });
      });
    }
    /**
     * Loads content from a URL and inserts it into the element at a specified position.
     * Can be called with two or three arguments:
     * - `insert(url: string, callback?: HandlerCallBackFn)`
     * - `insert(url: string, mode?: 'afterbegin' | 'afterend' | 'beforebegin' | 'beforeend', callback?: HandlerCallBackFn)`
     *
     * @param url - The URL to fetch content from.
     * @param modeOrCallback - Optional position to insert the content or a callback function.
     * @param callback - Optional callback function if mode is provided.
     * @returns The Be instance for method chaining.
     * @example
     * // Call with two arguments
     * be('#test').insertHttp('/content.html', ({ be }) => console.log(be.html));
     *
     * // Call with three arguments
     * be('#test').insertHttp('/content.html', 'afterbegin', ({ be }) => console.log(be.html));
     */
    async insert(url, modeOrCallback, callback) {
      let mode;
      if (typeof modeOrCallback === "function") {
        callback = modeOrCallback;
      } else {
        mode = modeOrCallback;
      }
      const response = await fetch(url);
      const content = await response.text();
      this.beElement.eachNode((el) => {
        const beElem = Be.elem(el);
        const domHandler = new DomHandler(beElem);
        domHandler.insert(mode || "beforeend", content);
        callback == null ? void 0 : callback({
          fragment: content,
          be: beElem,
          root: this.beElement
        });
      });
    }
  };
  __publicField(_HttpHandler, "methods", Object.values(httpMethods));
  var HttpHandler = _HttpHandler;

  // ../../../../idae/packages/idae-be/dist/be.js
  var Be = class _Be {
    constructor(input) {
      __publicField(this, "inputNode");
      __publicField(this, "isWhat");
      //
      __publicField(this, "timerOut", null);
      __publicField(this, "timerInterval", null);
      // styles
      __publicField(this, "styles");
      __publicField(this, "styleHandler");
      __publicField(this, "setStyle");
      __publicField(this, "getStyle");
      __publicField(this, "unsetStyle");
      // dataSet
      __publicField(this, "data");
      __publicField(this, "dataHandler");
      __publicField(this, "setData");
      __publicField(this, "getData");
      __publicField(this, "deleteData");
      __publicField(this, "getKey");
      // attributes
      __publicField(this, "attrs");
      __publicField(this, "attrHandler");
      __publicField(this, "setAttr");
      __publicField(this, "getAttr");
      __publicField(this, "deleteAttr");
      // position
      __publicField(this, "position");
      __publicField(this, "positionHandler");
      __publicField(this, "clonePosition");
      __publicField(this, "overlapPosition");
      __publicField(this, "snapTo");
      // dom
      __publicField(this, "dom");
      __publicField(this, "domHandler");
      __publicField(this, "update");
      __publicField(this, "append");
      __publicField(this, "prepend");
      __publicField(this, "insert");
      __publicField(this, "afterBegin");
      __publicField(this, "afterEnd");
      __publicField(this, "beforeBegin");
      __publicField(this, "beforeEnd");
      __publicField(this, "remove");
      __publicField(this, "replace");
      __publicField(this, "clear");
      __publicField(this, "normalize");
      __publicField(this, "wrap");
      __publicField(this, "unwrap");
      // text
      __publicField(this, "text");
      __publicField(this, "textHandler");
      __publicField(this, "appendText");
      __publicField(this, "prependText");
      __publicField(this, "updateText");
      __publicField(this, "replaceText");
      __publicField(this, "removeText");
      __publicField(this, "clearText");
      __publicField(this, "normalizeText");
      __publicField(this, "wrapText");
      // events
      __publicField(this, "events");
      __publicField(this, "eventHandler");
      __publicField(this, "on");
      __publicField(this, "off");
      __publicField(this, "fire");
      // classes
      __publicField(this, "classes");
      __publicField(this, "classesHandler");
      __publicField(this, "addClass");
      __publicField(this, "removeClass");
      __publicField(this, "toggleClass");
      __publicField(this, "replaceClass");
      // walk
      __publicField(this, "walk");
      __publicField(this, "walkHandler");
      __publicField(this, "up");
      __publicField(this, "next");
      __publicField(this, "without");
      __publicField(this, "previous");
      __publicField(this, "siblings");
      __publicField(this, "children");
      __publicField(this, "closest");
      __publicField(this, "lastChild");
      __publicField(this, "firstChild");
      __publicField(this, "find");
      __publicField(this, "findAll");
      // timers
      __publicField(this, "timers");
      __publicField(this, "timerHandler");
      __publicField(this, "timeout");
      __publicField(this, "interval");
      __publicField(this, "clearTimeout");
      __publicField(this, "clearInterval");
      // http
      __publicField(this, "http");
      __publicField(this, "httpHandler");
      __publicField(this, "updateHttp");
      __publicField(this, "insertHttp");
      if (input instanceof _Be) {
        return input;
      }
      this.inputNode = _Be.getNode(input);
      this.isWhat = typeof this.inputNode === "string" ? "qy" : Array.isArray(this.inputNode) ? "array" : "element";
      this.styleHandler = new StylesHandler(this);
      this.styles = this.handle(this.styleHandler);
      this.attach(StylesHandler, "Style");
      this.dataHandler = new DataHandler(this);
      this.data = this.handle(this.dataHandler);
      this.attach(DataHandler, "Data");
      this.attrHandler = new AttrHandler(this);
      this.attrs = this.handle(this.attrHandler);
      this.attach(AttrHandler, "Attr");
      this.positionHandler = new PositionHandler(this);
      this.position = this.handle(this.positionHandler);
      this.attach(PositionHandler);
      this.textHandler = new TextHandler(this);
      this.text = this.handle(this.textHandler);
      this.attach(TextHandler, "Text");
      this.domHandler = new DomHandler(this);
      this.dom = this.handle(this.domHandler);
      this.attach(DomHandler);
      this.eventHandler = new EventsHandler(this);
      this.events = this.handle(this.eventHandler);
      this.attach(EventsHandler);
      this.classesHandler = new ClassesHandler(this);
      this.classes = this.handle(this.classesHandler);
      this.attach(ClassesHandler, "Class");
      this.walkHandler = new WalkHandler(this);
      this.walk = this.handle(this.walkHandler);
      this.attach(WalkHandler);
      this.timerHandler = new TimersHandler(this);
      this.timers = this.handle(this.timerHandler);
      this.attach(TimersHandler);
      this.httpHandler = new HttpHandler(this);
      this.http = this.handle(this.httpHandler);
      this.attach(HttpHandler, "Http");
    }
    /**
     * Normalizes the input to ensure `node` is always an HTMLElement or an array of HTMLElements.
     * @param input - The input to normalize (string, HTMLElement, or array of HTMLElements).
     * @returns A valid HTMLElement or an array of HTMLElements.
     */
    static getNode(input) {
      if (typeof input === "string") {
        const elements = Array.from(document.querySelectorAll(input));
        return elements.length === 1 ? elements[0] : elements;
      } else if (input instanceof HTMLElement) {
        return input;
      } else if (Array.isArray(input)) {
        return input.filter((n) => n instanceof HTMLElement);
      }
      throw new Error("Invalid input: must be a string, HTMLElement, or an array of HTMLElements.");
    }
    static elem(node) {
      return new _Be(node);
    }
    static createBe(tagOrHtml, options) {
      const test = BeUtils.isHTML(tagOrHtml, { returnHTMLelement: true });
      const testIsTag = test.isHtml && !tagOrHtml.includes(" ") && tagOrHtml.length < 15;
      let ret;
      if (test.isHtml && test.beElem) {
        ret = test.beElem;
      } else if (testIsTag) {
        const el = document.createElement(tagOrHtml);
        ret = be(el);
      } else {
        const el = document.createElement("div");
        ret = be(el);
      }
      if (options == null ? void 0 : options.style)
        ret.setStyle(options.style);
      if (options == null ? void 0 : options.attributes)
        ret.setAttr(options.attributes);
      if (options == null ? void 0 : options.className)
        ret.addClass(options.className);
      return ret;
    }
    /**
     * Creates a new `Be` element based on the provided string or HTMLElement.
     * If the input is an HTMLElement, it creates a new `Be` element and sets it as the child of the provided element.
     * If the input is a string, it checks if it is a valid HTML string and creates a new `Be` element based on it.
     * If the input is neither a string nor an HTMLElement, it creates a new `Be` element with the default tag.
     *
     * @param str - The string or HTMLElement to create the `Be` element from.
     * @param options - Additional options for creating the `Be` element.
     * @returns The created `Be` element.
     */
    static toBe(str, options = {}) {
      const { tag = "div" } = options;
      let beElem;
      if (str instanceof HTMLElement) {
        beElem = be(str);
      } else if (typeof str === "string") {
        const trimmed = str.trim();
        if (trimmed.startsWith("<") && trimmed.endsWith(">") && trimmed.includes("</")) {
          const parser = new DOMParser();
          const doc = parser.parseFromString(trimmed, "text/html");
          const element = doc.body.firstElementChild || document.createElement(tag);
          beElem = createBe(tag);
          beElem.update(element.innerHTML);
          const styles = element.getAttribute("style");
          if (styles) {
            const styleObj = Object.fromEntries(styles.split(";").filter((style) => style.trim()).map((style) => {
              const [key, value] = style.split(":").map((s) => s.trim());
              return [key, value];
            }));
            beElem.setStyle(styleObj);
          }
          Array.from(element.attributes).forEach((attr) => {
            if (attr.name !== "style") {
              beElem.setAttr(attr.name, attr.value);
            }
          });
        } else {
          beElem = be(document.createElement(tag));
          beElem.update(str);
        }
      } else {
        beElem = createBe(tag);
      }
      return beElem;
    }
    fetch(options) {
      return fetch(options.url, {
        method: options.method || "GET",
        body: options.data ? JSON.stringify(options.data) : void 0,
        headers: options.headers || {}
      }).then((response) => response.json());
    }
    /**
     * Iterates over nodes based on the type of `this.isWhat` and applies a callback function to each node.
     *
     * @param callback - A function to be executed for each node. Receives the current node as an argument.
     * @param firstChild - Optional. If `true`, stops further iteration after the first child is processed.
     *
     * The behavior of the method depends on the value of `this.isWhat`:
     * - `'element'`: Applies the callback to a single HTMLElement (`this.inputNode`).
     * - `'array'`: Iterates over an array of HTMLElements (`this.inputNode`) and applies the callback to each.
     * - `'qy'`: Selects elements using a query selector string (`this.inputNode`) and applies the callback to each.
     */
    eachNode(callback, firstChild) {
      switch (this.isWhat) {
        case "element":
          BeUtils.applyCallback(this.inputNode, callback);
          break;
        case "array":
          this.inputNode.forEach((lo) => {
            BeUtils.applyCallback(lo, callback);
            if (firstChild)
              return;
          });
          break;
        case "qy":
          document.querySelectorAll(this.inputNode).forEach((el) => {
            callback(el);
            if (firstChild)
              return;
          });
          break;
      }
    }
    get html() {
      return this.isWhat === "element" ? this.inputNode.innerHTML : null;
    }
    get node() {
      switch (this.isWhat) {
        case "element":
          return this.inputNode;
        case "array":
          return Array.from(this.inputNode);
        case "qy":
          return Array.from(document.querySelectorAll(this.inputNode));
      }
    }
    attach(Handler, suffix = "") {
      const fromMethods = Handler.methods || [];
      fromMethods.forEach((method) => {
        const handler = new Handler(this);
        const methodName = suffix ? method + suffix : method;
        if (!(method in handler)) {
          console.error(`Method ${method} not found in ${Handler.name}`, handler);
        } else if (methodName in this) {
          if (!handler) {
            console.error(`Handler ${Handler.name} not found`, handler);
          }
          this[methodName] = (...args) => {
            return handler[method].apply(handler, args);
          };
        }
      });
    }
    handle(cl) {
      return cl.handle.bind(cl);
    }
  };
  var be = Be.elem;
  var toBe = Be.toBe;
  var beId = (id) => Be.elem(`#${id}`);
  var createBe = Be.createBe;

  // ../../../../idae/packages/idae-be/dist/dic/fragment.js
  var nodesList = {};
  var Fragments = class {
    create(tag, attributes) {
      if (!nodesList[tag]) {
        nodesList[tag] = document.createElement(tag);
      }
      const fragment = document.createDocumentFragment();
      const cloned = nodesList[tag].cloneNode(true);
      if (attributes) {
        Object.entries(attributes).forEach(([key, value]) => {
          cloned.setAttribute(key, value);
        });
      }
      fragment.appendChild(cloned);
      return fragment;
    }
  };
  __publicField(Fragments, "fragments", {});

  // ../../../../idae/packages/idae-be/dist/dic/proxyHandler.js
  var DynamicHandler = class {
    constructor(element, attr = "style") {
      __publicField(this, "beElement");
      __publicField(this, "attr");
      __publicField(this, "handler", {
        get: (target, prop) => {
          if (prop in target) {
            return Reflect.get(target, prop);
          }
          const matchSet = prop.match(/^set([A-Z]\w*)([A-Z]\w*)?$/);
          if (matchSet) {
            const [, mainProp, subProp] = matchSet;
            const cssProp = mainProp.toLowerCase();
            if (subProp) {
              const cssValue = subProp.toLowerCase();
              return () => {
                this.beElement.eachNode((el) => {
                  el[this.attr][cssProp] = cssValue;
                });
              };
            } else {
              return (value) => {
                this.beElement.eachNode((el) => {
                  if (value === void 0) {
                    el[this.attr][cssProp] = "";
                  } else {
                    el[this.attr][cssProp] = value;
                  }
                });
              };
            }
          }
          return void 0;
        }
      });
      this.beElement = element;
      this.attr = attr;
    }
    proxy() {
      return new Proxy(this, this.handler);
    }
  };
  return __toCommonJS(dist_exports);
})();
//# sourceMappingURL=idae-be.iife.js.map
