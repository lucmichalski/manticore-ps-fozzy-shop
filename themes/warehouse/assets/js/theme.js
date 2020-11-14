! function(t) {
    function e(n) {
        if (i[n]) return i[n].exports;
        var o = i[n] = {
            i: n,
            l: !1,
            exports: {}
        };
        return t[n].call(o.exports, o, o.exports, e), o.l = !0, o.exports
    }
    var i = {};
    e.m = t, e.c = i, e.i = function(t) {
        return t
    }, e.d = function(t, i, n) {
        e.o(t, i) || Object.defineProperty(t, i, {
            configurable: !1,
            enumerable: !0,
            get: n
        })
    }, e.n = function(t) {
        var i = t && t.__esModule ? function() {
            return t.default
        } : function() {
            return t
        };
        return e.d(i, "a", i), i
    }, e.o = function(t, e) {
        return Object.prototype.hasOwnProperty.call(t, e)
    }, e.p = "", e(e.s = 37)
}([function(t, e) {
    t.exports = jQuery
}, function(t, e) {
    t.exports = prestashop
}, function(t, e, i) {
    "use strict";

    function n(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = function() {
            function t(t, e) {
                for (var i = 0; i < e.length; i++) {
                    var n = e[i];
                    n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                }
            }
            return function(e, i, n) {
                return i && t(e.prototype, i), n && t(e, n), e
            }
        }(),
        s = i(0),
        r = (function(t) {
            t && t.__esModule
        }(s), function() {
            function t() {
                n(this, t)
            }
            return o(t, [{
                key: "init",
                value: function() {}
            }]), t
        }());
    e.default = r, t.exports = e.default
}, function(t, e, i) {
    "use strict";
    var n = i(32);
    t.exports = function(t, e, i) {
        return void 0 === i ? n(t, e, !1) : n(t, i, !1 !== e)
    }
}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }
    i(26), i(29), i(25), i(31), i(34), i(35), i(24), i(7), i(17), i(22), i(23), i(6);
    var o = i(15),
        s = n(o),
        r = i(16),
        a = n(r),
        l = i(13),
        c = n(l),
        d = i(9),
        u = n(d),
        f = i(10),
        p = n(f),
        h = i(11),
        m = n(h),
        g = i(2),
        v = n(g),
        y = i(14),
        w = n(y),
        _ = i(1),
        b = n(_),
        S = i(28),
        x = n(S);
    i(18), i(19), i(21), i(8);
    for (var T in x.default.prototype) b.default[T] = x.default.prototype[T];
    $(document).ready(function() {
        var t = $(".js-dropdown"),
            e = $(".js-block-toggle"),
            i = new m.default,
            n = new c.default(e),
            o = new p.default(t),
            r = new v.default,
            l = new w.default,
            d = new s.default,
            f = new a.default,
            h = new u.default;
        n.init(), h.init(), o.init(), i.init(), r.init(), l.init(), d.init(), f.init()
    })
}, function(t, e) {}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }

    function o() {
        r.default.each((0, r.default)(u), function(t, e) {
        if (parseFloat((0, r.default)(e).attr("min")) < 1) var decim_prod1 = 2;
        else var decim_prod1 = 0;
            (0, r.default)(e).TouchSpin({
                verticalbuttons: !0,
                verticalupclass: "fa fa-angle-up touchspin-up",
                verticaldownclass: "fa fa-angle-down touchspin-down",
                buttondown_class: "btn btn-touchspin  js-touchspin js-increase-product-quantity",
                buttonup_class: "btn btn-touchspin js-decrease-product-quantity",
                min: parseFloat((0, r.default)(e).attr("min")),
                max: 1e6,
                step: parseFloat((0, r.default)(e).attr("min")),
                decimals: decim_prod1
            })
        }), m.switchErrorStat()
    }
    var s = i(0),
        r = n(s),
        a = i(1),
        l = n(a),
        c = i(3),
        d = n(c);
    l.default.cart = l.default.cart || {}, l.default.cart.active_inputs = null;
    var u = 'input[name="product-quantity-spin"]',
        f = !1,
        p = !1,
        h = "";
    (0, r.default)(document).ready(function() {
        function t(t) {
            return "on.startupspin" === t || "on.startdownspin" === t
        }

        function e(t) {
            return "on.startupspin" === t
        }

        function i(t) {
            var e = t.parents(".bootstrap-touchspin").find(h);
            return e.is(":focus") ? null : e
        }

        function n(t) {
            var e = t.split("-"),
                i = void 0,
                n = void 0,
                o = "";
            for (i = 0; i < e.length; i++) n = e[i], 0 !== i && (n = n.substring(0, 1).toUpperCase() + n.substring(1)), o += n;
            return o
        }

        function s(o, s) {
            if (!t(s)) return {
                url: o.attr("href"),
                type: n(o.data("link-action"))
            };
            var r = i(o);
            if (r) {
                return e(s) ? {
                    url: r.data("up-url"),
                    type: "increaseProductQuantity"
                } : {
                    url: r.data("down-url"),
                    type: "decreaseProductQuantity"
                }
            }
        }

        function a(t, e, i, n) {
            return y(), r.default.ajax({
                url: t,
                method: "POST",
                data: e,
                dataType: "json",
                beforeSend: function(t) {
                    g.push(t)
                }
            }).then(function(t) {
                m.checkUpdateOpertation(t), i.val(t.quantity);
                var e;
                e = i && i.dataset ? i.dataset : t, l.default.emit("updateCart", {
                    reason: e
                })
            }).fail(function(t) {
                l.default.emit("handleError", {
                    eventType: "updateProductQuantityInCart",
                    resp: t
                })
            })
        }

        function c(t) {
            return {
                ajax: "1",
                qty: Math.abs(t),
                action: "update",
                op: f(t)
            }
        }

        function f(t) {
            return t > 0 ? "up" : "down"
        }

        function p(t) {
            var e = (0, r.default)(t.currentTarget),
                i = e.data("update-url"),
                n = e.attr("value"),
                o = e.val();
            if (o != parseFloat(o) || o < 0 || isNaN(o)) return void e.val(n);
            var s = o - n;
            0 !== s && (e.attr("value", o), a(i, c(s), e))
        }
        var h = ".js-cart-line-product-quantity",
            g = [];
        l.default.on("updateCart", function() {
            (0, r.default)(".quickview").modal("hide")
        }), l.default.on("updatedCart", function() {
            o()
        }), o();
        var v = (0, r.default)("body"),
            y = function() {
                for (var t; g.length > 0;) t = g.pop(), t.abort()
            },
            w = function(t) {
                return (0, r.default)(t.parents(".bootstrap-touchspin").find("input"))
            },
            _ = function(t) {
                t.preventDefault();
                var e = (0, r.default)(t.currentTarget),
                    i = t.currentTarget.dataset,
                    n = s(e, t.namespace),
                    o = {
                        ajax: "1",
                        action: "update"
                    };
                void 0 !== n && (y(), r.default.ajax({
                    url: n.url,
                    method: "POST",
                    data: o,
                    dataType: "json",
                    beforeSend: function(t) {
                        g.push(t)
                    }
                }).then(function(t) {
                    m.checkUpdateOpertation(t), w(e).val(t.quantity), l.default.emit("updateCart", {
                        reason: i
                    })
                }).fail(function(t) {
                    l.default.emit("handleError", {
                        eventType: "updateProductInCart",
                        resp: t,
                        cartAction: n.type
                    })
                }))
            };
        v.on("click", '[data-link-action="delete-from-cart"], [data-link-action="remove-voucher"]', _), v.on("touchspin.on.startdownspin", u, _), v.on("touchspin.on.startupspin", u, _), v.on("keyup", h, (0, d.default)(400, function(t) {
            p(t)
        })), v.on("click", ".js-discount .code", function(t) {
            t.stopPropagation();
            var e = (0, r.default)(t.currentTarget);
            return (0, r.default)("[name=discount_name]").val(e.text()), !1
        })
    });
    var m = {
        switchErrorStat: function() {
            var t = (0, r.default)(".checkout a");
            if (((0, r.default)("#notifications article.alert-danger").length || "" !== h && !f) && t.addClass("disabled"), "" !== h) {
                var e = ' <article class="alert alert-danger" role="alert" data-alert="danger"><ul><li>' + h + "</li></ul></article>";
                (0, r.default)("#notifications").html(e), h = "", p = !1
            } else !f && p && (f = !1, p = !1, (0, r.default)("#notifications").html(""), t.removeClass("disabled"))
        },
        checkUpdateOpertation: function(t) {
            f = t.hasOwnProperty("hasError");
            var e = t.errors || "";
            h = e instanceof Array ? e.join(" ") : e, p = !0
        }
    }
}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }

    function o() {
        0 !== (0, r.default)(".js-cancel-address").length && (0, r.default)(".checkout-step:not(.js-current-step) .step-title").addClass("not-allowed"), (0, r.default)(".js-terms a").on("click", function(t) {
            t.preventDefault();
            var e = (0, r.default)(t.target).attr("href");
            e && (e += "?content_only=1", r.default.get(e, function(t) {
                (0, r.default)("#modal").find(".modal-body").html((0, r.default)(t).find(".page-cms").contents())
            }).fail(function(t) {
                l.default.emit("handleError", {
                    eventType: "clickTerms",
                    resp: t
                })
            })), (0, r.default)("#modal").modal("show")
        }), (0, r.default)(".js-gift-checkbox").on("click", function(t) {
            (0, r.default)("#gift").collapse("toggle")
        })
    }
    var s = i(0),
        r = n(s),
        a = i(1),
        l = n(a);
    (0, r.default)(document).ready(function() {
        1 === (0, r.default)("body#checkout").length && o(), l.default.on("updatedDeliveryForm", function(t) {
            void 0 !== t.deliveryOption && 0 !== t.deliveryOption.length && ((0, r.default)(".carrier-extra-content").hide(), t.deliveryOption.next(".carrier-extra-content").slideDown())
        })
    })
}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }
    var o = i(1),
        s = n(o),
        r = i(0),
        a = n(r);
    s.default.blockcart = s.default.blockcart || {}, s.default.blockcart.showModal = function(t) {
        function e() {
            return (0, a.default)("#blockcart-modal-wrap")
        }
        "modal" == iqitTheme.cart_confirmation ? function() {
            var i = e();
            i.length && (i.remove(), (0, a.default)(".modal-backdrop.show").first().remove()), (0, a.default)("body").append(t), i = function() {
                return (0, a.default)("#blockcart-modal")
            }(), i.modal("show"), i.on("hide.bs.modal", function(t) {
                s.default.emit("updateProduct", {
                    reason: t.currentTarget.dataset,
                    event: t
                })
            }), i.on("shown.bs.modal", function(t) {
                var e = {
                    dots: !0,
                    accessibility: !1,
                    speed: 300,
                    arrows: !1,
                    autoplay: !0,
                    autoplaySpeed: 4500,
                    slidesToShow: 5,
                    slidesToScroll: 5,
                    responsive: [{
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3
                        }
                    }, {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    }]
                };
                i.find(".slick-crossselling-products").first().slick(e)
            })
        }() : "notification" == iqitTheme.cart_confirmation ? function() {
            var i = e();
            i.length && i.remove(), (0, a.default)("body").append(t), i = function() {
                return (0, a.default)("#blockcart-notification")
            }(), i.addClass("ns-show"), setTimeout(function() {
                i.addClass("ns-hide")
            }, 1300)
        }() : s.default.responsive.mobile ? ((0, a.default)("#mobile-cart-toogle").dropdown("toggle"), "floating" == iqitTheme.cart_style && (0, a.default)("body").animate({
            scrollTop: (0, a.default)("#mobile-cart-toogle").offset().top
        }, 300)) : ((0, a.default)("#cart-toogle").dropdown("toggle"), "floating" == iqitTheme.cart_style && (0, a.default)("body").animate({
            scrollTop: (0, a.default)("#blockcart").offset().top
        }, 300))
    }
}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }

    function o(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var s = function() {
            function t(t, e) {
                for (var i = 0; i < e.length; i++) {
                    var n = e[i];
                    n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                }
            }
            return function(e, i, n) {
                return i && t(e.prototype, i), n && t(e, n), e
            }
        }(),
        r = i(0),
        a = n(r),
        l = i(20),
        c = (n(l), function() {
            function t() {
                o(this, t)
            }
            return s(t, [{
                key: "init",
                value: function() {
                    var t = (0, a.default)("#search_widget"),
                        e = (0, a.default)("#search-widget-mobile"),
                        i = t.find("input[type=text]"),
                        n = e.find("input[type=text]"),
                        o = t.attr("data-search-controller-url"),
                        s = (0, a.default)("#header-search-btn"),
                        r = (0, a.default)("#mobile-btn-search");
                    this.autocomplete(t, i, o), this.autocomplete(e, n, o), s.on("shown.bs.dropdown", function() {
                        setTimeout(function() {
                            i.focus()
                        }, 300)
                    }), r.on("shown.bs.dropdown", function() {
                        setTimeout(function() {
                            (0, a.default)("#mobile-btn-search").find("input[type=text]").focus()
                        }, 300)
                    }), (0, a.default)("#fullscreen-search-backdrop").on("touchstart", function(t) {
                        t.stopPropagation(), (0, a.default)("#header-search-btn-drop").dropdown("toggle")
                    })
                }
            }, {
                key: "autocomplete",
                value: function(t, e, i) {
                    var n = void 0,
                        o = e.data("all-text");
                    e.autoComplete({
                        minChars: 2,
                        cache: !1,
                        source: function(t, e) {
                            try {
                                n.abort()
                            } catch (t) {}
                            n = a.default.post(i, {
                                s: t,
                                resultsPerPage: 10,
                                ajax: !0
                            }, null, "json").then(function(t) {
                                var i = {
                                    type: "all"
                                };
                                t.products.length >= 10 && t.products.push(i), e(t.products)
                            }).fail(e)
                        },
                        renderItem: function(t, e) {
                            if ("all" == t.type) return '<div class="autocomplete-suggestion autocomplete-suggestion-show-all dropdown-item" data-type="all" data-val="' + e + '"><div class="row no-gutters align-items-center"><div class="col"><span class="name">' + o + ' <i class="fa fa-angle-right" aria-hidden="true"></i></span></div></div></div>';
                            var i = "";
                            try {
                                i = '<div class="col col-auto col-img"><img class="img-fluid" src="' + t.cover.small.url + '" /></div>'
                            } catch (t) {
                                i = ""
                            }
                            return '<div class="autocomplete-suggestion dropdown-item" data-url="' + t.url + '"><div class="row no-gutters align-items-center">' + i + '<div class="col"><span class="name">' + t.name + '</span><span class="product-price">' + t.price + "</span></div></div></div>"
                        },
                        onSelect: function(e, i, n) {
                            "all" == n.data("type") ? t.find("form").submit() : window.location.href = n.data("url")
                        }
                    })
                }
            }]), t
        }());
    e.default = c, t.exports = e.default
}, function(t, e, i) {
    "use strict";

    function n(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = function() {
            function t(t, e) {
                for (var i = 0; i < e.length; i++) {
                    var n = e[i];
                    n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                }
            }
            return function(e, i, n) {
                return i && t(e.prototype, i), n && t(e, n), e
            }
        }(),
        s = i(0),
        r = function(t) {
            return t && t.__esModule ? t : {
                default: t
            }
        }(s),
        a = function() {
            function t(e) {
                n(this, t), this.el = e
            }
            return o(t, [{
                key: "init",
                value: function() {
                    this.el.find("select.link").each(function(t, e) {
                        (0, r.default)(e).on("change", function(t) {
                            window.location = (0, r.default)(this).val()
                        })
                    })
                }
            }]), t
        }();
    e.default = a, t.exports = e.default
}, function(t, e, i) {
    "use strict";

    function n(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = function() {
            function t(t, e) {
                for (var i = 0; i < e.length; i++) {
                    var n = e[i];
                    n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                }
            }
            return function(e, i, n) {
                return i && t(e.prototype, i), n && t(e, n), e
            }
        }(),
        s = i(0),
        r = function(t) {
            return t && t.__esModule ? t : {
                default: t
            }
        }(s),
        a = function() {
            function t() {
                n(this, t)
            }
            return o(t, [{
                key: "init",
                value: function() {
                    this.parentFocus(), this.togglePasswordVisibility()
                }
            }, {
                key: "parentFocus",
                value: function() {
                    (0, r.default)(".js-child-focus").focus(function() {
                        (0, r.default)(this).closest(".js-parent-focus").addClass("focus")
                    }), (0, r.default)(".js-child-focus").focusout(function() {
                        (0, r.default)(this).closest(".js-parent-focus").removeClass("focus")
                    })
                }
            }, {
                key: "togglePasswordVisibility",
                value: function() {
                    (0, r.default)('button[data-action="show-password"]').on("click", function() {
                        var t = (0, r.default)(this).closest(".input-group").children("input.js-visible-password");
                        "password" === t.attr("type") ? (t.attr("type", "text"), (0, r.default)(this).find("i").removeClass("fa-eye-slash").addClass("fa-eye")) : (t.attr("type", "password"), (0, r.default)(this).find("i").removeClass("fa-eye").addClass("fa-eye-slash"))
                    })
                }
            }]), t
        }();
    e.default = a, t.exports = e.default
}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = i(0),
        s = n(o),
        r = i(1),
        a = n(r),
        l = i(2),
        c = n(l);
    e.default = function(t) {
        var e = !1,
            i = void 0,
            n = (0, s.default)("#infinity-url"),
            o = !1,
            r = function() {
                var r = (0, s.default)("#js-product-list").find(".products")[0],
                    l = {
                        element: r,
                        handler: function(r) {
                            if ("down" == r) {
                                var d = n.attr("href"),
                                    u = [d, d.indexOf("?") >= 0 ? "&" : "?", "from-xhr"].join("");
                                t.addClass("-infinity-loading"), i.destroy(), e = !1, s.default.get(u, null, null, "json").then(function(r) {
                                    o = !1;
                                    var d = (0, s.default)("#js-product-list");
                                    d.find(".products").first().append((0, s.default)(r.rendered_products).find(".products").first().html()), d.find(".pagination").first().replaceWith((0, s.default)(r.rendered_products).find(".pagination").first()), (0, s.default)("#js-product-list-bottom").replaceWith(r.rendered_products_bottom), (new c.default).init(), t.removeClass("-infinity-loading"), a.default.emit("afterUpdateProductList");
                                    var u = (0, s.default)(r.rendered_products).find("#infinity-url");
                                    u.length && (n = u, i = new Waypoint(l), e = !0)
                                })
                            }
                        },
                        offset: "bottom-in-view"
                    };
                n.length && (i = new Waypoint(l))
            };
        r(), a.default.on("afterUpdateProductListFacets", function() {
            e && i.destroy(), n = (0, s.default)("#infinity-url"), r()
        })
    }, t.exports = e.default
}, function(t, e, i) {
    "use strict";

    function n(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = function() {
            function t(t, e) {
                for (var i = 0; i < e.length; i++) {
                    var n = e[i];
                    n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                }
            }
            return function(e, i, n) {
                return i && t(e.prototype, i), n && t(e, n), e
            }
        }(),
        s = i(0),
        r = function(t) {
            return t && t.__esModule ? t : {
                default: t
            }
        }(s),
        a = function() {
            function t(e) {
                n(this, t), this.el = e
            }
            return o(t, [{
                key: "init",
                value: function() {
                    this.el.find(".block-title").on("click", function(t, e) {
                        (0, r.default)(this).parent().toggleClass("_toggled")
                    })
                }
            }]), t
        }();
    e.default = a, t.exports = e.default
}, function(t, e, i) {
    "use strict";

    function n(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = function() {
            function t(t, e) {
                for (var i = 0; i < e.length; i++) {
                    var n = e[i];
                    n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                }
            }
            return function(e, i, n) {
                return i && t(e.prototype, i), n && t(e, n), e
            }
        }(),
        s = i(0),
        r = function(t) {
            return t && t.__esModule ? t : {
                default: t
            }
        }(s),
        a = function() {
            function t() {
                n(this, t)
            }
            return o(t, [{
                key: "init",
                value: function() {
                    var t = void 0,
                        e = void 0,
                        i = void 0,
                        n = (0, r.default)("#wrapper");
                    n.on("show.bs.modal", "#product-modal", function(e) {
                        var i = (0, r.default)("#product-images-large .slick-current img").first().data("image-large-src");
                        if ((0, r.default)(".js-modal-product-cover-easyzoom").first().attr("href", i), (0, r.default)(".js-modal-product-cover").first().attr("src", i), "inner" == iqitTheme.pp_zoom || "modalzoom" == iqitTheme.pp_zoom) {
                            var o = (0, r.default)(".easyzoom-modal").easyZoom();
                            t = o.data("easyZoom")
                        } else n.on("click", ".js-modal-product-cover-easyzoom", function(t) {
                            t.preventDefault()
                        })
                    }), n.on("shown.bs.modal", "#product-modal", function(t) {
                        i = (0, r.default)("#modal-product-thumbs"), e = i.slick({
                            slidesToShow: 10,
                            slidesToScroll: 10,
                            dots: !1,
                            arrows: !0,
                            focusOnSelect: !0,
                            responsive: [{
                                breakpoint: 575,
                                settings: {
                                    slidesToShow: 6,
                                    slidesToScroll: 6
                                }
                            }, {
                                breakpoint: 420,
                                settings: {
                                    slidesToShow: 5,
                                    slidesToScroll: 5
                                }
                            }]
                        })
                    }), n.on("hidden.bs.modal", "#product-modal", function(e) {
                        "inner" != iqitTheme.pp_zoom && "modalzoom" != iqitTheme.pp_zoom || t.teardown(), i.slick("unslick")
                    }), (0, r.default)("body").on("click", ".js-modal-thumb", function(e) {
                        "inner" == iqitTheme.pp_zoom || "modalzoom" == iqitTheme.pp_zoom ? t.swap((0, r.default)(e.target).data("image-large-src"), (0, r.default)(e.target).data("image-large-src")) : (0, r.default)(".js-modal-product-cover").attr("src", (0, r.default)(e.target).data("image-large-src"))
                    })
                }
            }]), t
        }();
    e.default = a, t.exports = e.default
}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }

    function o(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var s = function() {
            function t(t, e) {
                for (var i = 0; i < e.length; i++) {
                    var n = e[i];
                    n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                }
            }
            return function(e, i, n) {
                return i && t(e.prototype, i), n && t(e, n), e
            }
        }(),
        r = i(0),
        a = n(r),
        l = i(3),
        c = n(l),
        d = function() {
            function t() {
                o(this, t)
            }
            return s(t, [{
                key: "init",
                value: function() {
                    this.backToTop(), this.productCarousels(), this.otherCarousels(), iqitTheme.op_preloader && this.pagePreloader()
                }
            }, {
                key: "backToTop",
                value: function() {
                    var t = (0, a.default)("#back-to-top");
                    (0, a.default)(window).scroll((0, c.default)(300, function() {
                        (0, a.default)(this).scrollTop() > 300 ? t.addClass("-back-to-top-visible") : t.stop().removeClass("-back-to-top-visible")
                    })), t.on("click", function(t) {
                        t.preventDefault(), (0, a.default)("body, html").animate({
                            scrollTop: 0
                        }, 300)
                    })
                }
            }, {
                key: "productCarousels",
                value: function() {
                    var t = (0, a.default)(".slick-default-carousel"),
                        e = {
                            dots: !0,
                            accessibility: !1,
                            speed: 300,
                            autoplay: iqitTheme.pl_crsl_autoplay,
                            autoplaySpeed: 4500,
                            slidesToShow: iqitTheme.pl_slider_ld,
                            slidesToScroll: iqitTheme.pl_slider_ld,
                            infinite: !1,
                            responsive: [{
                                breakpoint: 1200,
                                settings: {
                                    slidesToShow: iqitTheme.pl_slider_d,
                                    slidesToScroll: iqitTheme.pl_slider_d
                                }
                            }, {
                                breakpoint: 768,
                                settings: {
                                    slidesToShow: iqitTheme.pl_slider_t,
                                    slidesToScroll: iqitTheme.pl_slider_t
                                }
                            }, {
                                breakpoint: 576,
                                settings: {
                                    slidesToShow: iqitTheme.pl_slider_p,
                                    slidesToScroll: iqitTheme.pl_slider_p
                                }
                            }]
                        };
                    t.each(function() {
                        var t = (0, a.default)(this),
                            i = a.default.extend({}, e, t.data("slider_options"));
                        t.slick(i)
                    })
                }
            }, {
                key: "otherCarousels",
                value: function() {
                    (0, a.default)(".js-iqithtmlandbanners-block-banner-slider").slick({
                        arrows: !1,
                        autoplay: !0,
                        autoplaySpeed: 5e3,
                        dots: !0
                    })
                }
            }, {
                key: "pagePreloader",
                value: function() {
                    (0, a.default)(window).load(function() {
                        (0, a.default)("#page-preloader").fadeOut("slow", function() {
                            (0, a.default)(this).remove()
                        }), (0, a.default)("#main-page-content").removeAttr("style")
                    })
                }
            }]), t
        }();
    e.default = d, t.exports = e.default
}, function(t, e, i) {
    "use strict";

    function n(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }
    Object.defineProperty(e, "__esModule", {
        value: !0
    });
    var o = function() {
            function t(t, e) {
                for (var i = 0; i < e.length; i++) {
                    var n = e[i];
                    n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                }
            }
            return function(e, i, n) {
                return i && t(e.prototype, i), n && t(e, n), e
            }
        }(),
        s = i(0),
        r = function(t) {
            return t && t.__esModule ? t : {
                default: t
            }
        }(s),
        a = function() {
            function t() {
                n(this, t)
            }
            return o(t, [{
                key: "init",
                value: function() {
                    if (6 != iqitTheme.h_layout && 7 != iqitTheme.h_layout && ("menu" != iqitTheme.h_sticky && "header" != iqitTheme.h_sticky || this.stickyHeader(iqitTheme.h_sticky)), iqitTheme.f_fixed) {
                        !!window.MSInputMethodContext && !!document.documentMode || (0, r.default)("#footer").footerReveal({
                            shadow: !1,
                            zIndex: -1
                        })
                    }
                    "ontouchstart" in document.documentElement && (0, r.default)("body").addClass("touch-device")
                }
            }, {
                key: "stickyHeader",
                value: function(t) {
                    var e = void 0,
                        i = void 0,
                        n = void 0,
                        o = void 0,
                        s = void 0;
                    if ("menu" == t ? (e = (0, r.default)("#iqitmegamenu-wrapper"), i = (0, r.default)("#sticky-cart-wrapper"), n = (0, r.default)("#ps-shoppingcart-wrapper"), o = (0, r.default)("#ps-shoppingcart"), s = function(t) {
                            "down" == t ? i.append(o) : n.append(o)
                        }) : (e = (0, r.default)("#desktop-header"), s = function(t) {}), e.length) {
                        new Waypoint.Sticky({
                            element: e[0],
                            wrapper: '<div class="sticky-desktop-wrapper" />',
                            stuckClass: "stuck stuck-" + t,
                            handler: s,
                            offset: 0
                        })
                    }
                }
            }]), t
        }();
    e.default = a, t.exports = e.default
}, function(t, e, i) {
    "use strict";

    function n() {
        (0, r.default)("#order-return-form table thead input[type=checkbox]").on("click", function() {
            var t = (0, r.default)(this).prop("checked");
            (0, r.default)("#order-return-form table tbody input[type=checkbox]").each(function(e, i) {
                (0, r.default)(i).prop("checked", t)
            })
        })
    }

    function o() {
        (0, r.default)("body#order-detail") && n()
    }
    var s = i(0),
        r = function(t) {
            return t && t.__esModule ? t : {
                default: t
            }
        }(s);
    (0, r.default)(document).ready(o)
}, function(t, e, i) {
    "use strict";
    ! function(t) {
        var e = 0,
            i = function(e, i) {
                this.options = i, this.$elementFilestyle = [], this.$element = t(e)
            };
        i.prototype = {
            clear: function() {
                this.$element.val(""), this.$elementFilestyle.find(":text").val(""), this.$elementFilestyle.find(".badge").remove()
            },
            destroy: function() {
                this.$element.removeAttr("style").removeData("filestyle"), this.$elementFilestyle.remove()
            },
            disabled: function(t) {
                if (!0 === t) this.options.disabled || (this.$element.attr("disabled", "true"), this.$elementFilestyle.find("label").attr("disabled", "true"), this.options.disabled = !0);
                else {
                    if (!1 !== t) return this.options.disabled;
                    this.options.disabled && (this.$element.removeAttr("disabled"), this.$elementFilestyle.find("label").removeAttr("disabled"), this.options.disabled = !1)
                }
            },
            buttonBefore: function(t) {
                if (!0 === t) this.options.buttonBefore || (this.options.buttonBefore = !0, this.options.input && (this.$elementFilestyle.remove(), this.constructor(), this.pushNameFiles()));
                else {
                    if (!1 !== t) return this.options.buttonBefore;
                    this.options.buttonBefore && (this.options.buttonBefore = !1, this.options.input && (this.$elementFilestyle.remove(), this.constructor(), this.pushNameFiles()))
                }
            },
            icon: function(t) {
                if (!0 === t) this.options.icon || (this.options.icon = !0, this.$elementFilestyle.find("label").prepend(this.htmlIcon()));
                else {
                    if (!1 !== t) return this.options.icon;
                    this.options.icon && (this.options.icon = !1, this.$elementFilestyle.find(".icon-span-filestyle").remove())
                }
            },
            input: function(t) {
                if (!0 === t) this.options.input || (this.options.input = !0, this.options.buttonBefore ? this.$elementFilestyle.append(this.htmlInput()) : this.$elementFilestyle.prepend(this.htmlInput()), this.$elementFilestyle.find(".badge").remove(), this.pushNameFiles(), this.$elementFilestyle.find(".group-span-filestyle").addClass("input-group-btn"));
                else {
                    if (!1 !== t) return this.options.input;
                    if (this.options.input) {
                        this.options.input = !1, this.$elementFilestyle.find(":text").remove();
                        var e = this.pushNameFiles();
                        e.length > 0 && this.options.badge && this.$elementFilestyle.find("label").append(' <span class="badge">' + e.length + "</span>"), this.$elementFilestyle.find(".group-span-filestyle").removeClass("input-group-btn")
                    }
                }
            },
            size: function(t) {
                if (void 0 === t) return this.options.size;
                var e = this.$elementFilestyle.find("label"),
                    i = this.$elementFilestyle.find("input");
                e.removeClass("btn-lg btn-sm"), i.removeClass("input-lg input-sm"), "nr" != t && (e.addClass("btn-" + t), i.addClass("input-" + t))
            },
            placeholder: function(t) {
                if (void 0 === t) return this.options.placeholder;
                this.options.placeholder = t, this.$elementFilestyle.find("input").attr("placeholder", t)
            },
            buttonText: function(t) {
                if (void 0 === t) return this.options.buttonText;
                this.options.buttonText = t, this.$elementFilestyle.find("label .buttonText").html(this.options.buttonText)
            },
            buttonName: function(t) {
                if (void 0 === t) return this.options.buttonName;
                this.options.buttonName = t, this.$elementFilestyle.find("label").attr({
                    class: "btn " + this.options.buttonName
                })
            },
            iconName: function(t) {
                if (void 0 === t) return this.options.iconName;
                this.$elementFilestyle.find(".icon-span-filestyle").attr({
                    class: "icon-span-filestyle " + this.options.iconName
                })
            },
            htmlIcon: function() {
                return this.options.icon ? '<span class="icon-span-filestyle ' + this.options.iconName + '"></span> ' : ""
            },
            htmlInput: function() {
                return this.options.input ? '<input type="text" class="form-control ' + ("nr" == this.options.size ? "" : "input-" + this.options.size) + '" placeholder="' + this.options.placeholder + '" disabled> ' : ""
            },
            pushNameFiles: function() {
                var t = "",
                    e = [];
                void 0 === this.$element[0].files ? e[0] = {
                    name: this.$element[0] && this.$element[0].value
                } : e = this.$element[0].files;
                for (var i = 0; i < e.length; i++) t += e[i].name.split("\\").pop() + ", ";
                return "" !== t ? this.$elementFilestyle.find(":text").val(t.replace(/\, $/g, "")) : this.$elementFilestyle.find(":text").val(""), e
            },
            constructor: function() {
                var i = this,
                    n = "",
                    o = i.$element.attr("id"),
                    s = "";
                "" !== o && o || (o = "filestyle-" + e, i.$element.attr({
                    id: o
                }), e++), s = '<span class="group-span-filestyle ' + (i.options.input ? "input-group-btn" : "") + '"><label for="' + o + '" class="btn ' + i.options.buttonName + " " + ("nr" == i.options.size ? "" : "btn-" + i.options.size) + '" ' + (i.options.disabled ? 'disabled="true"' : "") + ">" + i.htmlIcon() + '<span class="buttonText">' + i.options.buttonText + "</span></label></span>", n = i.options.buttonBefore ? s + i.htmlInput() : i.htmlInput() + s, i.$elementFilestyle = t('<div class="bootstrap-filestyle input-group">' + n + "</div>"), i.$elementFilestyle.find(".group-span-filestyle").attr("tabindex", "0").keypress(function(t) {
                    if (13 === t.keyCode || 32 === t.charCode) return i.$elementFilestyle.find("label").click(), !1
                }), i.$element.css({
                    position: "absolute",
                    clip: "rect(0px 0px 0px 0px)"
                }).attr("tabindex", "-1").after(i.$elementFilestyle), i.options.disabled && i.$element.attr("disabled", "true"), i.$element.change(function() {
                    var t = i.pushNameFiles();
                    0 == i.options.input && i.options.badge ? 0 == i.$elementFilestyle.find(".badge").length ? i.$elementFilestyle.find("label").append(' <span class="badge">' + t.length + "</span>") : 0 == t.length ? i.$elementFilestyle.find(".badge").remove() : i.$elementFilestyle.find(".badge").html(t.length) : i.$elementFilestyle.find(".badge").remove()
                }), window.navigator.userAgent.search(/firefox/i) > -1 && i.$elementFilestyle.find("label").click(function() {
                    return i.$element.click(), !1
                })
            }
        };
        var n = t.fn.filestyle;
        t.fn.filestyle = function(e, n) {
            var o = "",
                s = this.each(function() {
                    if ("file" === t(this).attr("type")) {
                        var s = t(this),
                            r = s.data("filestyle"),
                            a = t.extend({}, t.fn.filestyle.defaults, e, "object" == typeof e && e);
                        r || (s.data("filestyle", r = new i(this, a)), r.constructor()), "string" == typeof e && (o = r[e](n))
                    }
                });
            return void 0 !== typeof o ? o : s
        }, t.fn.filestyle.defaults = {
            buttonText: "Choose file",
            iconName: "fa fa-folder-open",
            buttonName: "btn-default",
            size: "nr",
            input: !0,
            badge: !0,
            icon: !0,
            buttonBefore: !1,
            disabled: !1,
            placeholder: ""
        }, t.fn.filestyle.noConflict = function() {
            return t.fn.filestyle = n, this
        }, t(function() {
            t(".filestyle").each(function() {
                var e = t(this),
                    i = {
                        input: "false" !== e.attr("data-input"),
                        icon: "false" !== e.attr("data-icon"),
                        buttonBefore: "true" === e.attr("data-buttonBefore"),
                        disabled: "true" === e.attr("data-disabled"),
                        size: e.attr("data-size"),
                        buttonText: e.attr("data-buttonText"),
                        buttonName: e.attr("data-buttonName"),
                        iconName: e.attr("data-iconName"),
                        badge: "false" !== e.attr("data-badge"),
                        placeholder: e.attr("data-placeholder")
                    };
                e.filestyle(i)
            })
        })
    }(window.jQuery)
}, function(t, e, i) {
    "use strict";
    ! function(t) {
        t.fn.footerReveal = function(e) {
            var i = t(this),
                n = i.prev(),
                o = t(window),
                s = t.extend({
                    shadow: !0,
                    shadowOpacity: .8,
                    zIndex: -100
                }, e);
            return t.extend(!0, {}, s, e), i.outerHeight() <= o.outerHeight() && i.offset().top >= o.outerHeight() && (i.css({
                "z-index": s.zIndex,
                position: "fixed",
                bottom: 0
            }), s.shadow && n.css({
                "-moz-box-shadow": "0 20px 30px -20px rgba(0,0,0," + s.shadowOpacity + ")",
                "-webkit-box-shadow": "0 20px 30px -20px rgba(0,0,0," + s.shadowOpacity + ")",
                "box-shadow": "0 20px 30px -20px rgba(0,0,0," + s.shadowOpacity + ")"
            }), o.on("load resize footerRevealResize", function() {
                i.css({
                    width: n.outerWidth()
                }), n.css({
                    "margin-bottom": i.outerHeight()
                })
            })), this
        }
    }(jQuery)
}, function(t, e, i) {
    "use strict";
    ! function(t) {
        t.fn.autoComplete = function(e) {
            var i = t.extend({}, t.fn.autoComplete.defaults, e);
            return "string" == typeof e ? (this.each(function() {
                var i = t(this);
                "destroy" == e && (i.off("blur.autocomplete focus.autocomplete keydown.autocomplete keyup.autocomplete"), i.data("autocomplete") ? i.attr("autocomplete", i.data("autocomplete")) : i.removeAttr("autocomplete"), t(i.data("sc")).remove(), i.removeData("sc").removeData("autocomplete"))
            }), this) : this.each(function() {
                function e(t) {
                    var e = n.val();
                    if (n.cache[e] = t, t.length && e.length >= i.minChars) {
                        for (var o = "", s = 0; s < t.length; s++) o += i.renderItem(t[s], e);
                        n.sc.html(o), n.updateSC(0)
                    } else n.sc.hide()
                }
                var n = t(this);
                n.sc = t('<div class="autocomplete-suggestions ' + i.menuClass + '"></div>'), n.data("sc", n.sc).data("autocomplete", n.attr("autocomplete")), n.attr("autocomplete", "off"), n.cache = {}, n.last_val = "", n.updateSC = function(t, e) {
                    n.sc.show()
                }, n.sc.insertAfter(n.parent()), n.sc.on("mouseleave", ".autocomplete-suggestion", function() {
                    t(".autocomplete-suggestion.selected").removeClass("selected")
                }), n.sc.on("mouseenter", ".autocomplete-suggestion", function() {
                    t(".autocomplete-suggestion.selected").removeClass("selected"), t(this).addClass("selected")
                }), n.sc.on("mousedown click", ".autocomplete-suggestion", function(e) {
                    var o = t(this),
                        s = o.data("val");
                    return (s || o.hasClass("autocomplete-suggestion")) && (n.val(s), i.onSelect(e, s, o), n.sc.hide()), !1
                }), n.on("blur.autocomplete", function() {
                    n.sc.hide()
                }), i.minChars || n.on("focus.autocomplete", function() {
                    n.last_val = "\n", n.trigger("keyup.autocomplete")
                }), n.on("keydown.autocomplete", function(e) {
                    if ((40 == e.which || 38 == e.which) && n.sc.html()) {
                        var o, s = t(".autocomplete-suggestion.selected", n.sc);
                        return s.length ? (o = 40 == e.which ? s.next(".autocomplete-suggestion") : s.prev(".autocomplete-suggestion"), o.length ? (s.removeClass("selected"), n.val(o.addClass("selected").data("val"))) : (s.removeClass("selected"), n.val(n.last_val), o = 0)) : (o = 40 == e.which ? t(".autocomplete-suggestion", n.sc).first() : t(".autocomplete-suggestion", n.sc).last(), n.val(o.addClass("selected").data("val"))), n.updateSC(0, o), !1
                    }
                    if (27 == e.which) n.val(n.last_val).sc.hide();
                    else if (13 == e.which || 9 == e.which) {
                        var s = t(".autocomplete-suggestion.selected", n.sc);
                        s.length && n.sc.is(":visible") && (i.onSelect(e, s.data("val"), s), setTimeout(function() {
                            n.sc.hide()
                        }, 20))
                    }
                }), n.on("keyup.autocomplete", function(o) {
                    if (!~t.inArray(o.which, [13, 27, 35, 36, 37, 38, 39, 40])) {
                        var s = n.val();
                        if (s.length >= i.minChars) {
                            if (s != n.last_val) {
                                if (n.last_val = s, clearTimeout(n.timer), i.cache) {
                                    if (s in n.cache) return void e(n.cache[s]);
                                    for (var r = 1; r < s.length - i.minChars; r++) {
                                        var a = s.slice(0, s.length - r);
                                        if (a in n.cache && !n.cache[a].length) return void e([])
                                    }
                                }
                                n.timer = setTimeout(function() {
                                    i.source(s, e)
                                }, i.delay)
                            }
                        } else n.last_val = s, n.sc.hide()
                    }
                })
            })
        }, t.fn.autoComplete.defaults = {
            source: 0,
            minChars: 3,
            delay: 150,
            cache: 1,
            menuClass: "",
            renderItem: function(t, e) {
                e = e.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&");
                var i = new RegExp("(" + e.split(" ").join("|") + ")", "gi");
                return '<div class="autocomplete-suggestion" data-val="' + t + '">' + t.replace(i, "<b>$1</b>") + "</div>"
            },
            onSelect: function(t, e, i) {}
        }
    }(jQuery)
}, function(t, e, i) {
    "use strict";
    ! function(t, e, i) {
        function n(e, n) {
            this.$element = t(e), this.options = t.extend({}, r, n), this.options.scrollHeight !== i && (this.options.height = this.$element.height()), this._defaults = r, this._name = o, this.init()
        }
        var o = "stickyUpHeader",
            s = (e.document, t(e)),
            r = {
                showClasses: "visible-stuck-up visible-stuck-up-scroll",
                temporaryHideClasses: "visible-stuck-up",
                hideClasses: "visible-stuck-up-scroll",
                throttleTimeout: 500
            };
        n.prototype.init = function() {
            var t = 0,
                e = this;
            s.on("scroll", e.throttle(this.options.throttleTimeout, function() {
                var i = s.scrollTop();
                i <= 0 ? e.hide() : i < t ? i > 400 ? e.show() : e.hide() : e.hideTemp(), t = i
            }))
        }, n.prototype.throttle = function(t, e) {
            var i, n;
            return function() {
                var o = this,
                    s = arguments,
                    r = +new Date;
                i && r < i + t ? (clearTimeout(n), n = setTimeout(function() {
                    i = r, e.apply(o, s)
                }, t)) : (i = r, e.apply(o, s))
            }
        }, n.prototype.hide = function() {
            return this.$element.removeClass(this.options.hideClasses)
        }, n.prototype.hideTemp = function() {
            return this.$element.removeClass(this.options.temporaryHideClasses)
        }, n.prototype.show = function() {
            return this.$element.addClass(this.options.showClasses)
        }, t.fn[o] = function(e) {
            return this.each(function() {
                t.data(this, o) || t.data(this, o, new n(this, e))
            })
        }
    }(jQuery, window)
}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }

    function o(t) {
        (0, r.default)("#search_filters").replaceWith(t.rendered_facets), (0, r.default)("#js-active-search-filters").replaceWith(t.rendered_active_filters), (0, r.default)("#js-product-list-top").replaceWith(t.rendered_products_top), (0, r.default)("#js-product-list").replaceWith(t.rendered_products), (0, r.default)("#js-product-list-bottom").replaceWith(t.rendered_products_bottom), t.rendered_products_header && (0, r.default)("#js-product-list-header").replaceWith(t.rendered_products_header), (new d.default).init()
    }
    var s = i(0),
        r = n(s),
        a = i(1),
        l = n(a),
        c = i(2),
        d = n(c),
        u = i(33),
        f = n(u),
        p = i(12),
        h = n(p);
    (0, r.default)(document).ready(function() {
        var t = (0, r.default)("body"),
            e = (0, r.default)("#products");
        l.default.iqitLazyLoad = new f.default({
            elements_selector: ".js-lazy-product-image",
            threshold: 600,
            class_loading: "lazy-product-loading"
        }), iqitTheme.pl_infinity && (0, h.default)(e), (0, r.default)(document).ready(function() {
            t.on("click", ".js-quick-view-iqit", function(t) {
                var e = (0, r.default)(t.target).closest(".js-product-miniature");
                l.default.emit("clickQuickView", {
                    miniature: e,
                    dataset: e.data()
                }), t.preventDefault()
            })
        }), l.default.on("clickQuickView", function(e) {
            var n = {
                    action: "quickview",
                    id_product: e.dataset.idProduct,
                    id_product_attribute: e.dataset.idProductAttribute
                },
                o = e.miniature.parent().prev().children(".js-product-miniature").first(),
                s = e.miniature.parent().next().children(".js-product-miniature").first();
            r.default.post(l.default.urls.pages.product, n, null, "json").then(function(e) {
                var n = (0, r.default)(".modal.quickview").first();
                n.length ? i(t, e, o, s, n) : i(t, e, o, s, !1)
            }).fail(function(t) {
                l.default.emit("handleError", {
                    eventType: "clickQuickView",
                    resp: t
                })
            })
        });
        var i = function(t, e, i, n, o) {
            var s = function(t) {
                var e = !1;
                if ("inner" == iqitTheme.pp_zoom && (e = !0), f = t.find("#product-images-large"), f.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: !0,
                        fade: e,
                        lazyLoad: "ondemand"
                    }), "inner" == iqitTheme.pp_zoom) {
                    t.find(".easyzoom-product").easyZoom()
                } else t.find(".js-easyzoom-trigger").on("click", function(t) {
                    t.preventDefault()
                });
                (0, r.default)(".modal-backdrop.show").first().addClass("backdrop-second"), l.default.emit("quickViewShown", {
                    modal: t
                })
            };
            o ? o.find("#quickview-modal-product-content").replaceWith((0, r.default)(e.quickview_html).find("#quickview-modal-product-content")) : t.append(e.quickview_html);
            var a = (0, r.default)("#quickview-modal"),
                c = (0, r.default)("#js-quickview-prev-btn"),
                d = (0, r.default)("#js-quickview-next-btn");
            i.length || c.hide(), n.length || d.hide(), c.on("click", function(t) {
                var e = i;
                l.default.emit("clickQuickView", {
                    miniature: e,
                    dataset: e.data()
                }), t.preventDefault()
            }), d.on("click", function(t) {
                var e = n;
                l.default.emit("clickQuickView", {
                    miniature: e,
                    dataset: e.data()
                }), t.preventDefault()
            }), o ? s(a) : (a.modal("show"), a.on("shown.bs.modal", function() {
                s(a)
            }), a.on("hide.bs.modal", function() {
                a.remove(), (0, r.default)("#iqitsizecharts-modal").remove()
            }));
            var u = (0, r.default)("#iqitsizecharts-modal"),
                f = void 0;
            a.find("#quantity_wanted").TouchSpin({
                verticalbuttons: !0,
                verticalupclass: "fa fa-angle-up touchspin-up",
                verticaldownclass: "fa fa-angle-down touchspin-down",
                buttondown_class: "btn btn-touchspin js-touchspin",
                buttonup_class: "btn btn-touchspin js-touchspin",
                min: parseFloat((this).attr("min")),
                max: 1e6,
                step: parseFloat((this).attr("min")),
                decimals: 2
            }), u.on("show.bs.modal", function() {
                u.detach(), u.appendTo("body"), (0, r.default)(this).addClass("fv-modal-stack"), a.addClass("quickview-second-modal")
            })
        };
        t.on("click", "#search_filter_toggler", function() {
            (0, r.default)("#search_filters_wrapper").removeClass("hidden-sm-down"), (0, r.default)("#content-wrapper").addClass("hidden-sm-down"), (0, r.default)("#footer").addClass("hidden-sm-down"), (0, r.default)("#left-column, #right-column").addClass("-only-facet-search")
        }), t.on("click", "#search_center_filter_toggler", function() {
            (0, r.default)("#facets_search_center").slideToggle("100")
        }), (0, r.default)("#search_filter_controls .ok, #search_filter_controls .js-search-filters-clear-all").on("click", function() {
            (0, r.default)("#search_filters_wrapper").addClass("hidden-sm-down"), (0, r.default)("#content-wrapper").removeClass("hidden-sm-down"), (0, r.default)("#footer").removeClass("hidden-sm-down"), (0, r.default)("#left-column, #right-column").removeClass("-only-facet-search"), l.default.iqitLazyLoad.update()
        });
        var n = function(t) {
            if (void 0 !== t.target.dataset.searchUrl) return t.target.dataset.searchUrl;
            if (void 0 === (0, r.default)(t.target).parent()[0].dataset.searchUrl) throw new Error("Can not parse search URL");
            return (0, r.default)(t.target).parent()[0].dataset.searchUrl
        };
        t.on("change", "#search_filters input[data-search-url]", function(t) {
            l.default.emit("updateFacets", n(t))
        }), t.on("click", ".js-search-filters-clear-all", function(t) {
            l.default.emit("updateFacets", n(t))
        }), t.on("click", ".js-search-link", function(t) {
            t.preventDefault(), l.default.emit("updateFacets", (0, r.default)(t.target).closest("a").get(0).href)
        }), t.on("change", "#search_filters select", function(t) {
            var e = (0, r.default)(t.target).closest("form");
            l.default.emit("updateFacets", "?" + e.serialize())
        }), t.on("click", '[data-button-action="change-list-view"]', function(t) {}), l.default.on("updateFacets", function() {
            e.addClass("-facets-loading")
        }), l.default.on("updateProductList", function(t) {
            o(t), l.default.emit("afterUpdateProductListFacets"), l.default.emit("afterUpdateProductList")
        }), l.default.on("afterUpdateProductList", function(t) {
            "ontouchstart" in document.documentElement || ((0, r.default)("body > .tooltip.bs-tooltip-top").remove(), (0, r.default)(function() {
                (0, r.default)('[data-toggle="tooltip"]').tooltip()
            })), l.default.iqitLazyLoad.update(), e.removeClass("-facets-loading")
        })
    })
}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }
    var o = i(0),
        s = n(o);
    i(27);
    var r = i(1),
        a = n(r);
    (0, s.default)(document).ready(function() {
        function t() {
            var t = !1;
            if ("inner" == iqitTheme.pp_zoom && (t = !0), (0, s.default)("#product-images-large").slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: !0,
                    infinite: !0,
                    fade: t,
                    lazyLoad: "ondemand",
                    asNavFor: "#product-images-thumbs"
                }), "inner" == iqitTheme.pp_zoom) {
                (0, s.default)(".easyzoom-product").easyZoom()
            } else(0, s.default)(".js-easyzoom-trigger").on("click", function(t) {
                t.preventDefault()
            })
        }

        function e() {
            var t = !1,
                e = 5,
                i = "ondemand",
                n = [];
            "left" != iqitTheme.pp_thumbs && "leftd" != iqitTheme.pp_thumbs || (t = !0, e = 4, i = "progressive"), "leftd" == iqitTheme.pp_thumbs && (n = [{
                breakpoint: 769,
                settings: {
                    slidesToShow: 5,
                    slidesToScroll: 5,
                    vertical: !1,
                    verticalSwiping: !1
                }
            }]), (0, s.default)("#product-images-thumbs").slick({
                slidesToShow: e,
                slidesToScroll: e,
                infinite: !1,
                asNavFor: "#product-images-large",
                dots: !1,
                arrows: !0,
                vertical: t,
                verticalSwiping: t,
                focusOnSelect: !0,
                lazyLoad: i,
                responsive: n
            })
        }

        function i() {
            var t = (0, s.default)(".js-file-input");
            t.filestyle({
                buttonText: t.data("buttontext")
            }), t.on("change", function(t) {
                var e = void 0,
                    i = void 0;
                (e = (0, s.default)(t.currentTarget)[0]) && (i = e.files[0]) && (0, s.default)(e).prev().text(i.name)
            })
        }

        function n() {
            "ontouchstart" in document.documentElement || (0, s.default)(function() {
                (0, s.default)('[data-toggle="tooltip"]').tooltip()
            })
        }! function() {
            var t = (0, s.default)("#quantity_wanted");
            if (parseFloat(t.attr("min")) < 1) var decim_prod2 = 2;
            else var decim_prod2 = 0;
            t.TouchSpin({
                verticalbuttons: !0,
                verticalupclass: "fa fa-angle-up touchspin-up",
                verticaldownclass: "fa fa-angle-down touchspin-down",
                buttondown_class: "btn btn-touchspin js-touchspin",
                buttonup_class: "btn btn-touchspin js-touchspin",
                min: parseFloat(t.attr("min")),
                max: 1e6,
                step: parseFloat(t.attr("min")),
                decimals: decim_prod2
            }), (0, s.default)("body").on("input touchspin.on.stopspin", "#quantity_wanted", function(t) {
                a.default.emit("updateProduct", {
                    eventType: "updatedProductQuantity",
                    event: t
                })
            })
        }(), i(), t(), e(), n(),
            function() {
                (0, s.default)("#product-accessories-sidebar").slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    rows: 5,
                    dots: !0,
                    arrows: !1,
                    accessibility: !1,
                    speed: 300,
                    autoplay: iqitTheme.pl_crsl_autoplay,
                    autoplaySpeed: 4500
                })
            }();
        var o = (0, s.default)("#main");
        "tabha" == iqitTheme.pp_tabs && function(t, e) {
            var i = void 0,
                n = void 0;
            if ((0, s.default)(window).width() < e) {
                var o = "";
                i = (0, s.default)(t + " li").toArray(), n = (0, s.default)("#product-infos-tabs-content").find(".tab-pane").toArray(), jQuery.each(i, function(t, e) {
                    o += '<div class="card"><div class="nav-tabs" role="tab" >', o += t > 0 ? '<a class="nav-link collapsed" id="ma-nav-link-' + t + '" data-toggle="collapse" data-parent="#product-infos-accordion-mobile" href="#product-infos-accordion-mobile-' + t + '">' : '<a class="nav-link" id="ma-nav-link-' + t + '" data-toggle="collapse" data-parent="#product-infos-accordion-mobile" href="#product-infos-accordion-mobile-' + t + '">', o += e.innerText + '<i class="fa fa-angle-down float-right angle-down" aria-hidden="true"></i><i class="fa fa-angle-up float-right angle-up" aria-hidden="true"></i></a>', o += "</div>", o += t > 0 ? '<div id="product-infos-accordion-mobile-' + t + '" class="collapse tab-content" role="tabpanel">' : '<div id="product-infos-accordion-mobile-' + t + '" class="collapse tab-content show" role="tabpanel">', o += '<div class="">' + n[t].innerHTML + "</div>", o += "</div>"
                }), (0, s.default)("#product-infos-accordion-mobile").html(o), a.default.iqitLazyLoad.update(), (0, s.default)(t).remove(), (0, s.default)("#product-infos-tabs-content").remove()
            }
        }("#product-infos-tabs", 576), (0, s.default)("body").on("click", '[data-button-action="add-to-cart"]', function(t) {
            t.preventDefault(), (0, s.default)(t.target).addClass("processing-add")
        }), a.default.on("updateCart", function(t) {
            (0, s.default)(".add-to-cart.processing-add").removeClass("processing-add")
        }), a.default.on("updateProduct", function(t) {
            void 0 !== a.default.page.page_name && "product" == a.default.page.page_name && o.addClass("-combinations-loading")
        }), a.default.on("updatedProduct", function(r) {
            if (i(), n(), t(), r && r.product_minimal_quantity) {
                var a = parseFloat(r.product_minimal_quantity);
                (0, s.default)("#quantity_wanted").trigger("touchspin.updatesettings", {
                    min: a
                })
            }
            e(), (0, s.default)((0, s.default)(".tabs .nav-link.active").attr("href")).addClass("active").removeClass("fade"), (0, s.default)(".js-product-images-modal").replaceWith(r.product_images_modal), o.removeClass("-combinations-loading")
        })
    })
}, function(t, e, i) {
    "use strict";

    function n(t) {
        return t && t.__esModule ? t : {
            default: t
        }
    }

    function o(t, e) {
        var i = e.children().detach();
        e.empty().append(t.children().detach()), t.append(i)
    }

    function s() {
        u.default.responsive.mobile ? (0, c.default)("*[id^='_desktop_']").each(function(t, e) {
            var i = (0, c.default)("#" + e.id.replace("_desktop_", "_mobile_"));
            i && o((0, c.default)(e), i)
        }) : (0, c.default)("*[id^='_mobile_']").each(function(t, e) {
            var i = (0, c.default)("#" + e.id.replace("_mobile_", "_desktop_"));
            i && o((0, c.default)(e), i)
        }), u.default.emit("responsive update", {
            mobile: u.default.responsive.mobile
        })
    }

    function r() {
        u.default.responsive.mobile && (0, c.default)("*[id^='_desktop_blockcart']").each(function(t, e) {
            var i = (0, c.default)("#" + e.id.replace("_desktop_blockcart", "_mobile_blockcart"));
            i && o((0, c.default)(e), i)
        })
    }

    function a(t) {
        var e = (0, c.default)("#mobile-header-sticky");
        if ("up" == t && e.stickyUpHeader(), e.length) {
            new Waypoint.Sticky({
                element: e[0],
                wrapper: '<div class="sticky-mobile-wrapper" />',
                stuckClass: "stuck stuck-" + t
            })
        }
    }
    var l = i(0),
        c = n(l),
        d = i(1),
        u = n(d);
    u.default.responsive = u.default.responsive || {}, u.default.responsive.current_width = window.innerWidth, u.default.responsive.min_width = 991, u.default.responsive.mobile = u.default.responsive.current_width <= u.default.responsive.min_width, (0, c.default)(window).on("resize", function() {
        var t = u.default.responsive.current_width,
            e = u.default.responsive.min_width,
            i = window.innerWidth,
            n = t >= e && i < e || t < e && i >= e;
        u.default.responsive.current_width = i, u.default.responsive.mobile = u.default.responsive.current_width <= u.default.responsive.min_width, n && s()
    }), (0, c.default)(document).ready(function() {
        1 == iqitTheme.rm_breakpoint && (u.default.responsive.min_width = 5e3, u.default.responsive.mobile = !0), u.default.responsive.mobile && s(), u.default.on("responsive updateAjax", function(t) {
            r()
        }), "up" != iqitTheme.rm_sticky && "down" != iqitTheme.rm_sticky || a(iqitTheme.rm_sticky)
    })
}, function(t, e, i) {
    "use strict";
    ! function(t) {
        function e(t, e) {
            return t + ".touchspin_" + e
        }

        function i(i, n) {
            return t.map(i, function(t) {
                return e(t, n)
            })
        }
        var n = 0;
        t.fn.TouchSpin = function(e) {
            if ("destroy" === e) return void this.each(function() {
                var e = t(this),
                    n = e.data();
                t(document).off(i(["mouseup", "touchend", "touchcancel", "mousemove", "touchmove", "scroll", "scrollstart"], n.spinnerid).join(" "))
            });
            var o = {
                    min: 0,
                    max: 100,
                    initval: "",
                    replacementval: "",
                    step: 0.05,
                    decimals: 0,
                    stepinterval: 100,
                    forcestepdivisibility: "round",
                    stepintervaldelay: 500,
                    verticalbuttons: !1,
                    verticalupclass: "glyphicon glyphicon-chevron-up",
                    verticaldownclass: "glyphicon glyphicon-chevron-down",
                    prefix: "",
                    postfix: "",
                    prefix_extraclass: "",
                    postfix_extraclass: "",
                    booster: !0,
                    boostat: 10,
                    maxboostedstep: !1,
                    mousewheel: !0,
                    buttondown_class: "btn btn-default",
                    buttonup_class: "btn btn-default",
                    buttondown_txt: "-",
                    buttonup_txt: "+"
                },
                s = {
                    min: "min",
                    max: "max",
                    initval: "init-val",
                    replacementval: "replacement-val",
                    step: "step",
                    decimals: "decimals",
                    stepinterval: "step-interval",
                    verticalbuttons: "vertical-buttons",
                    verticalupclass: "vertical-up-class",
                    verticaldownclass: "vertical-down-class",
                    forcestepdivisibility: "force-step-divisibility",
                    stepintervaldelay: "step-interval-delay",
                    prefix: "prefix",
                    postfix: "postfix",
                    prefix_extraclass: "prefix-extra-class",
                    postfix_extraclass: "postfix-extra-class",
                    booster: "booster",
                    boostat: "boostat",
                    maxboostedstep: "max-boosted-step",
                    mousewheel: "mouse-wheel",
                    buttondown_class: "button-down-class",
                    buttonup_class: "button-up-class",
                    buttondown_txt: "button-down-txt",
                    buttonup_txt: "button-up-txt"
                };
            return this.each(function() {
                function r() {
                    "" !== C.initval && "" === L.val() && L.val(C.initval)
                }

                function a(t) {
                    d(t), w();
                    var e = A.input.val();
                    "" !== e && (e = Number(A.input.val()), A.input.val(e.toFixed(C.decimals)))
                }

                function l() {
                    C = t.extend({}, o, P, c(), e)
                }

                function c() {
                    var e = {};
                    return t.each(s, function(t, i) {
                        var n = "bts-" + i;
                        L.is("[data-" + n + "]") && (e[t] = L.data(n))
                    }), e
                }

                function d(e) {
                    C = t.extend({}, C, e)
                }

                function u() {
                    var t = L.val(),
                        e = L.parent();
                    "" !== t && (t = Number(t).toFixed(C.decimals)), L.data("initvalue", t).val(t), L.addClass("form-control"), e.hasClass("input-group") ? f(e) : p()
                }

                function f(e) {
                    e.addClass("bootstrap-touchspin");
                    var i, n, o = L.prev(),
                        s = L.next(),
                        r = '<span class="input-group-addon bootstrap-touchspin-prefix">' + C.prefix + "</span>",
                        a = '<span class="input-group-addon bootstrap-touchspin-postfix">' + C.postfix + "</span>";
                    o.hasClass("input-group-btn") ? (i = '<button class="' + C.buttondown_class + ' bootstrap-touchspin-down" type="button">' + C.buttondown_txt + "</button>", o.append(i)) : (i = '<span class="input-group-btn"><button class="' + C.buttondown_class + ' bootstrap-touchspin-down" type="button">' + C.buttondown_txt + "</button></span>", t(i).insertBefore(L)), s.hasClass("input-group-btn") ? (n = '<button class="' + C.buttonup_class + ' bootstrap-touchspin-up" type="button">' + C.buttonup_txt + "</button>", s.prepend(n)) : (n = '<span class="input-group-btn"><button class="' + C.buttonup_class + ' bootstrap-touchspin-up" type="button">' + C.buttonup_txt + "</button></span>", t(n).insertAfter(L)), t(r).insertBefore(L), t(a).insertAfter(L), E = e
                }

                function p() {
                    var e;
                    e = C.verticalbuttons ? '<div class="input-group bootstrap-touchspin"><span class="input-group-addon bootstrap-touchspin-prefix">' + C.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + C.postfix + '</span><span class="input-group-btn-vertical"><button class="' + C.buttondown_class + ' bootstrap-touchspin-up" type="button"><i class="' + C.verticalupclass + '"></i></button><button class="' + C.buttonup_class + ' bootstrap-touchspin-down" type="button"><i class="' + C.verticaldownclass + '"></i></button></span></div>' : '<div class="input-group bootstrap-touchspin"><span class="input-group-btn"><button class="' + C.buttondown_class + ' bootstrap-touchspin-down" type="button">' + C.buttondown_txt + '</button></span><span class="input-group-addon bootstrap-touchspin-prefix">' + C.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + C.postfix + '</span><span class="input-group-btn"><button class="' + C.buttonup_class + ' bootstrap-touchspin-up" type="button">' + C.buttonup_txt + "</button></span></div>", E = t(e).insertBefore(L), t(".bootstrap-touchspin-prefix", E).after(L), L.hasClass("input-sm") ? E.addClass("input-group-sm") : L.hasClass("input-lg") && E.addClass("input-group-lg")
                }

                function h() {
                    A = {
                        down: t(".bootstrap-touchspin-down", E),
                        up: t(".bootstrap-touchspin-up", E),
                        input: t("input", E),
                        prefix: t(".bootstrap-touchspin-prefix", E).addClass(C.prefix_extraclass),
                        postfix: t(".bootstrap-touchspin-postfix", E).addClass(C.postfix_extraclass)
                    }
                }

                function m() {
                    "" === C.prefix && A.prefix.hide(), "" === C.postfix && A.postfix.hide()
                }

                function g() {
                    L.on("keydown", function(t) {
                        var e = t.keyCode || t.which;
                        38 === e ? ("up" !== H && (b(), T()), t.preventDefault()) : 40 === e && ("down" !== H && (S(), x()), t.preventDefault())
                    }), L.on("keyup", function(t) {
                        var e = t.keyCode || t.which;
                        38 === e ? k() : 40 === e && k()
                    }), L.on("blur", function() {
                        w()
                    }), A.down.on("keydown", function(t) {
                        var e = t.keyCode || t.which;
                        32 !== e && 13 !== e || ("down" !== H && (S(), x()), t.preventDefault())
                    }), A.down.on("keyup", function(t) {
                        var e = t.keyCode || t.which;
                        32 !== e && 13 !== e || k()
                    }), A.up.on("keydown", function(t) {
                        var e = t.keyCode || t.which;
                        32 !== e && 13 !== e || ("up" !== H && (b(), T()), t.preventDefault())
                    }), A.up.on("keyup", function(t) {
                        var e = t.keyCode || t.which;
                        32 !== e && 13 !== e || k()
                    }), A.down.on("mousedown.touchspin", function(t) {
                        A.down.off("touchstart.touchspin"), L.is(":disabled") || (S(), x(), t.preventDefault(), t.stopPropagation())
                    }), A.down.on("touchstart.touchspin", function(t) {
                        A.down.off("mousedown.touchspin"), L.is(":disabled") || (S(), x(), t.preventDefault(), t.stopPropagation())
                    }), A.up.on("mousedown.touchspin", function(t) {
                        A.up.off("touchstart.touchspin"), L.is(":disabled") || (b(), T(), t.preventDefault(), t.stopPropagation())
                    }), A.up.on("touchstart.touchspin", function(t) {
                        A.up.off("mousedown.touchspin"), L.is(":disabled") || (b(), T(), t.preventDefault(), t.stopPropagation())
                    }), A.up.on("mouseout touchleave touchend touchcancel", function(t) {
                        H && (t.stopPropagation(), k())
                    }), A.down.on("mouseout touchleave touchend touchcancel", function(t) {
                        H && (t.stopPropagation(), k())
                    }), A.down.on("mousemove touchmove", function(t) {
                        H && (t.stopPropagation(), t.preventDefault())
                    }), A.up.on("mousemove touchmove", function(t) {
                        H && (t.stopPropagation(), t.preventDefault())
                    }), t(document).on(i(["mouseup", "touchend", "touchcancel"], n).join(" "), function(t) {
                        H && (t.preventDefault(), k())
                    }), t(document).on(i(["mousemove", "touchmove", "scroll", "scrollstart"], n).join(" "), function(t) {
                        H && (t.preventDefault(), k())
                    }), L.on("mousewheel DOMMouseScroll", function(t) {
                        if (C.mousewheel && L.is(":focus")) {
                            var e = t.originalEvent.wheelDelta || -t.originalEvent.deltaY || -t.originalEvent.detail;
                            t.stopPropagation(), t.preventDefault(), e < 0 ? S() : b()
                        }
                    })
                }

                function v() {
                    L.on("touchspin.uponce", function() {
                        k(), b()
                    }), L.on("touchspin.downonce", function() {
                        k(), S()
                    }), L.on("touchspin.startupspin", function() {
                        T()
                    }), L.on("touchspin.startdownspin", function() {
                        x()
                    }), L.on("touchspin.stopspin", function() {
                        k()
                    }), L.on("touchspin.updatesettings", function(t, e) {
                        a(e)
                    })
                }

                function y(t) {
                    switch (C.forcestepdivisibility) {
                        case "round":
                            return (Math.round(t / C.step) * C.step).toFixed(C.decimals);
                        case "floor":
                            return (Math.floor(t / C.step) * C.step).toFixed(C.decimals);
                        case "ceil":
                            return (Math.ceil(t / C.step) * C.step).toFixed(C.decimals);
                        default:
                            return t
                    }
                }

                function w() {
                    var t, e, i;
                    if ("" === (t = L.val())) return void("" !== C.replacementval && (L.val(C.replacementval), L.trigger("change")));
                    C.decimals > 0 && "." === t || (e = parseFloat(t), isNaN(e) && (e = "" !== C.replacementval ? C.replacementval : 0), i = e, e.toString() !== t && (i = e), e < C.min && (i = C.min), e > C.max && (i = C.max), i = y(i), Number(t).toString() !== i.toString() && (L.val(i), L.trigger("change")))
                }

                function _() {
                    if (C.booster) {
                        var t = Math.pow(2, Math.floor(j / C.boostat)) * C.step;
                        return C.maxboostedstep && t > C.maxboostedstep && (t = C.maxboostedstep, O = Math.round(O / t) * t), Math.max(C.step, t)
                    }
                    return C.step
                }

                function b() {
                    w(), O = parseFloat(A.input.val()), isNaN(O) && (O = 0);
                    var t = O,
                        e = _();
                    O += e, O > C.max && (O = C.max, L.trigger("touchspin.on.max"), k()), A.input.val(Number(O).toFixed(C.decimals)), t !== O && L.trigger("change")
                }

                function S() {
                    w(), O = parseFloat(A.input.val()), isNaN(O) && (O = 0);
                    var t = O,
                        e = _();
                    O -= e, O < C.min && (O = C.min, L.trigger("touchspin.on.min"), k()), A.input.val(O.toFixed(C.decimals)), t !== O && L.trigger("change")
                }

                function x() {
                    k(), j = 0, H = "down", L.trigger("touchspin.on.startspin"), L.trigger("touchspin.on.startdownspin"), D = setTimeout(function() {
                        I = setInterval(function() {
                            j++, S()
                        }, C.stepinterval)
                    }, C.stepintervaldelay)
                }

                function T() {
                    k(), j = 0, H = "up", L.trigger("touchspin.on.startspin"), L.trigger("touchspin.on.startupspin"), N = setTimeout(function() {
                        $ = setInterval(function() {
                            j++, b()
                        }, C.stepinterval)
                    }, C.stepintervaldelay)
                }

                function k() {
                    switch (clearTimeout(D), clearTimeout(N), clearInterval(I), clearInterval($), H) {
                        case "up":
                            L.trigger("touchspin.on.stopupspin"), L.trigger("touchspin.on.stopspin");
                            break;
                        case "down":
                            L.trigger("touchspin.on.stopdownspin"), L.trigger("touchspin.on.stopspin")
                    }
                    j = 0, H = !1
                }
                var C, E, A, O, I, $, D, N, L = t(this),
                    P = L.data(),
                    j = 0,
                    H = !1;
                ! function() {
                    L.data("alreadyinitialized") || (L.data("alreadyinitialized", !0), n += 1, L.data("spinnerid", n), L.is("input") && (l(), r(), w(), u(), h(), m(), g(), v(), A.input.css("display", "block")))
                }()
            })
        }
    }(jQuery)
}, function(t, e, i) {
    "use strict";
    ! function(t, n) {
        n(e, i(30), i(0))
    }(0, function(t, e, i) {
        function n(t, e) {
            for (var i = 0; i < e.length; i++) {
                var n = e[i];
                n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
            }
        }

        function o(t, e, i) {
            return e && n(t.prototype, e), i && n(t, i), t
        }

        function s(t) {
            for (var e = 1; e < arguments.length; e++) {
                var i = null != arguments[e] ? arguments[e] : {},
                    n = Object.keys(i);
                "function" == typeof Object.getOwnPropertySymbols && (n = n.concat(Object.getOwnPropertySymbols(i).filter(function(t) {
                    return Object.getOwnPropertyDescriptor(i, t).enumerable
                }))), n.forEach(function(e) {
                    var n, o, s;
                    n = t, s = i[o = e], o in n ? Object.defineProperty(n, o, {
                        value: s,
                        enumerable: !0,
                        configurable: !0,
                        writable: !0
                    }) : n[o] = s
                })
            }
            return t
        }

        function r(t) {
            var e = this,
                n = !1;
            return i(this).one(l.TRANSITION_END, function() {
                n = !0
            }), setTimeout(function() {
                n || l.triggerTransitionEnd(e)
            }, t), this
        }
        e = e && e.hasOwnProperty("default") ? e.default : e, i = i && i.hasOwnProperty("default") ? i.default : i;
        var a = "transitionend",
            l = {
                TRANSITION_END: "bsTransitionEnd",
                getUID: function(t) {
                    for (; t += ~~(1e6 * Math.random()), document.getElementById(t););
                    return t
                },
                getSelectorFromElement: function(t) {
                    var e = t.getAttribute("data-target");
                    if (!e || "#" === e) {
                        var i = t.getAttribute("href");
                        e = i && "#" !== i ? i.trim() : ""
                    }
                    return e && document.querySelector(e) ? e : null
                },
                getTransitionDurationFromElement: function(t) {
                    if (!t) return 0;
                    var e = i(t).css("transition-duration"),
                        n = i(t).css("transition-delay"),
                        o = parseFloat(e),
                        s = parseFloat(n);
                    return o || s ? (e = e.split(",")[0], n = n.split(",")[0], 1e3 * (parseFloat(e) + parseFloat(n))) : 0
                },
                reflow: function(t) {
                    return t.offsetHeight
                },
                triggerTransitionEnd: function(t) {
                    i(t).trigger(a)
                },
                supportsTransitionEnd: function() {
                    return Boolean(a)
                },
                isElement: function(t) {
                    return (t[0] || t).nodeType
                },
                typeCheckConfig: function(t, e, i) {
                    for (var n in i)
                        if (Object.prototype.hasOwnProperty.call(i, n)) {
                            var o = i[n],
                                s = e[n],
                                r = s && l.isElement(s) ? "element" : (a = s, {}.toString.call(a).match(/\s([a-z]+)/i)[1].toLowerCase());
                            if (!new RegExp(o).test(r)) throw new Error(t.toUpperCase() + ': Option "' + n + '" provided type "' + r + '" but expected type "' + o + '".')
                        } var a
                },
                findShadowRoot: function(t) {
                    if (!document.documentElement.attachShadow) return null;
                    if ("function" != typeof t.getRootNode) return t instanceof ShadowRoot ? t : t.parentNode ? l.findShadowRoot(t.parentNode) : null;
                    var e = t.getRootNode();
                    return e instanceof ShadowRoot ? e : null
                }
            };
        i.fn.emulateTransitionEnd = r, i.event.special[l.TRANSITION_END] = {
            bindType: a,
            delegateType: a,
            handle: function(t) {
                if (i(t.target).is(this)) return t.handleObj.handler.apply(this, arguments)
            }
        };
        var c = "alert",
            d = "bs.alert",
            u = "." + d,
            f = i.fn[c],
            p = {
                CLOSE: "close" + u,
                CLOSED: "closed" + u,
                CLICK_DATA_API: "click" + u + ".data-api"
            },
            h = function() {
                function t(t) {
                    this._element = t
                }
                var e = t.prototype;
                return e.close = function(t) {
                    var e = this._element;
                    t && (e = this._getRootElement(t)), this._triggerCloseEvent(e).isDefaultPrevented() || this._removeElement(e)
                }, e.dispose = function() {
                    i.removeData(this._element, d), this._element = null
                }, e._getRootElement = function(t) {
                    var e = l.getSelectorFromElement(t),
                        n = !1;
                    return e && (n = document.querySelector(e)), n || (n = i(t).closest(".alert")[0]), n
                }, e._triggerCloseEvent = function(t) {
                    var e = i.Event(p.CLOSE);
                    return i(t).trigger(e), e
                }, e._removeElement = function(t) {
                    var e = this;
                    if (i(t).removeClass("show"), i(t).hasClass("fade")) {
                        var n = l.getTransitionDurationFromElement(t);
                        i(t).one(l.TRANSITION_END, function(i) {
                            return e._destroyElement(t, i)
                        }).emulateTransitionEnd(n)
                    } else this._destroyElement(t)
                }, e._destroyElement = function(t) {
                    i(t).detach().trigger(p.CLOSED).remove()
                }, t._jQueryInterface = function(e) {
                    return this.each(function() {
                        var n = i(this),
                            o = n.data(d);
                        o || (o = new t(this), n.data(d, o)), "close" === e && o[e](this)
                    })
                }, t._handleDismiss = function(t) {
                    return function(e) {
                        e && e.preventDefault(), t.close(this)
                    }
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }]), t
            }();
        i(document).on(p.CLICK_DATA_API, '[data-dismiss="alert"]', h._handleDismiss(new h)), i.fn[c] = h._jQueryInterface, i.fn[c].Constructor = h, i.fn[c].noConflict = function() {
            return i.fn[c] = f, h._jQueryInterface
        };
        var m = "button",
            g = "bs.button",
            v = "." + g,
            y = ".data-api",
            w = i.fn[m],
            _ = "active",
            b = '[data-toggle^="button"]',
            S = ".btn",
            x = {
                CLICK_DATA_API: "click" + v + y,
                FOCUS_BLUR_DATA_API: "focus" + v + y + " blur" + v + y
            },
            T = function() {
                function t(t) {
                    this._element = t
                }
                var e = t.prototype;
                return e.toggle = function() {
                    var t = !0,
                        e = !0,
                        n = i(this._element).closest('[data-toggle="buttons"]')[0];
                    if (n) {
                        var o = this._element.querySelector('input:not([type="hidden"])');
                        if (o) {
                            if ("radio" === o.type)
                                if (o.checked && this._element.classList.contains(_)) t = !1;
                                else {
                                    var s = n.querySelector(".active");
                                    s && i(s).removeClass(_)
                                } if (t) {
                                if (o.hasAttribute("disabled") || n.hasAttribute("disabled") || o.classList.contains("disabled") || n.classList.contains("disabled")) return;
                                o.checked = !this._element.classList.contains(_), i(o).trigger("change")
                            }
                            o.focus(), e = !1
                        }
                    }
                    e && this._element.setAttribute("aria-pressed", !this._element.classList.contains(_)), t && i(this._element).toggleClass(_)
                }, e.dispose = function() {
                    i.removeData(this._element, g), this._element = null
                }, t._jQueryInterface = function(e) {
                    return this.each(function() {
                        var n = i(this).data(g);
                        n || (n = new t(this), i(this).data(g, n)), "toggle" === e && n[e]()
                    })
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }]), t
            }();
        i(document).on(x.CLICK_DATA_API, b, function(t) {
            t.preventDefault();
            var e = t.target;
            i(e).hasClass("btn") || (e = i(e).closest(S)), T._jQueryInterface.call(i(e), "toggle")
        }).on(x.FOCUS_BLUR_DATA_API, b, function(t) {
            var e = i(t.target).closest(S)[0];
            i(e).toggleClass("focus", /^focus(in)?$/.test(t.type))
        }), i.fn[m] = T._jQueryInterface, i.fn[m].Constructor = T, i.fn[m].noConflict = function() {
            return i.fn[m] = w, T._jQueryInterface
        };
        var k = "carousel",
            C = "bs.carousel",
            E = "." + C,
            A = ".data-api",
            O = i.fn[k],
            I = {
                interval: 5e3,
                keyboard: !0,
                slide: !1,
                pause: "hover",
                wrap: !0,
                touch: !0
            },
            $ = {
                interval: "(number|boolean)",
                keyboard: "boolean",
                slide: "(boolean|string)",
                pause: "(string|boolean)",
                wrap: "boolean",
                touch: "boolean"
            },
            D = "next",
            N = "prev",
            L = {
                SLIDE: "slide" + E,
                SLID: "slid" + E,
                KEYDOWN: "keydown" + E,
                MOUSEENTER: "mouseenter" + E,
                MOUSELEAVE: "mouseleave" + E,
                TOUCHSTART: "touchstart" + E,
                TOUCHMOVE: "touchmove" + E,
                TOUCHEND: "touchend" + E,
                POINTERDOWN: "pointerdown" + E,
                POINTERUP: "pointerup" + E,
                DRAG_START: "dragstart" + E,
                LOAD_DATA_API: "load" + E + A,
                CLICK_DATA_API: "click" + E + A
            },
            P = "active",
            j = ".active.carousel-item",
            H = ".carousel-indicators",
            B = {
                TOUCH: "touch",
                PEN: "pen"
            },
            M = function() {
                function t(t, e) {
                    this._items = null, this._interval = null, this._activeElement = null, this._isPaused = !1, this._isSliding = !1, this.touchTimeout = null, this.touchStartX = 0, this.touchDeltaX = 0, this._config = this._getConfig(e), this._element = t, this._indicatorsElement = this._element.querySelector(H), this._touchSupported = "ontouchstart" in document.documentElement || 0 < navigator.maxTouchPoints, this._pointerEvent = Boolean(window.PointerEvent || window.MSPointerEvent), this._addEventListeners()
                }
                var e = t.prototype;
                return e.next = function() {
                    this._isSliding || this._slide(D)
                }, e.nextWhenVisible = function() {
                    !document.hidden && i(this._element).is(":visible") && "hidden" !== i(this._element).css("visibility") && this.next()
                }, e.prev = function() {
                    this._isSliding || this._slide(N)
                }, e.pause = function(t) {
                    t || (this._isPaused = !0), this._element.querySelector(".carousel-item-next, .carousel-item-prev") && (l.triggerTransitionEnd(this._element), this.cycle(!0)), clearInterval(this._interval), this._interval = null
                }, e.cycle = function(t) {
                    t || (this._isPaused = !1), this._interval && (clearInterval(this._interval), this._interval = null), this._config.interval && !this._isPaused && (this._interval = setInterval((document.visibilityState ? this.nextWhenVisible : this.next).bind(this), this._config.interval))
                }, e.to = function(t) {
                    var e = this;
                    this._activeElement = this._element.querySelector(j);
                    var n = this._getItemIndex(this._activeElement);
                    if (!(t > this._items.length - 1 || t < 0))
                        if (this._isSliding) i(this._element).one(L.SLID, function() {
                            return e.to(t)
                        });
                        else {
                            if (n === t) return this.pause(), void this.cycle();
                            var o = n < t ? D : N;
                            this._slide(o, this._items[t])
                        }
                }, e.dispose = function() {
                    i(this._element).off(E), i.removeData(this._element, C), this._items = null, this._config = null, this._element = null, this._interval = null, this._isPaused = null, this._isSliding = null, this._activeElement = null, this._indicatorsElement = null
                }, e._getConfig = function(t) {
                    return t = s({}, I, t), l.typeCheckConfig(k, t, $), t
                }, e._handleSwipe = function() {
                    var t = Math.abs(this.touchDeltaX);
                    if (!(t <= 40)) {
                        var e = t / this.touchDeltaX;
                        0 < e && this.prev(), e < 0 && this.next()
                    }
                }, e._addEventListeners = function() {
                    var t = this;
                    this._config.keyboard && i(this._element).on(L.KEYDOWN, function(e) {
                        return t._keydown(e)
                    }), "hover" === this._config.pause && i(this._element).on(L.MOUSEENTER, function(e) {
                        return t.pause(e)
                    }).on(L.MOUSELEAVE, function(e) {
                        return t.cycle(e)
                    }), this._addTouchEventListeners()
                }, e._addTouchEventListeners = function() {
                    var t = this;
                    if (this._touchSupported) {
                        var e = function(e) {
                                t._pointerEvent && B[e.originalEvent.pointerType.toUpperCase()] ? t.touchStartX = e.originalEvent.clientX : t._pointerEvent || (t.touchStartX = e.originalEvent.touches[0].clientX)
                            },
                            n = function(e) {
                                t._pointerEvent && B[e.originalEvent.pointerType.toUpperCase()] && (t.touchDeltaX = e.originalEvent.clientX - t.touchStartX), t._handleSwipe(), "hover" === t._config.pause && (t.pause(), t.touchTimeout && clearTimeout(t.touchTimeout), t.touchTimeout = setTimeout(function(e) {
                                    return t.cycle(e)
                                }, 500 + t._config.interval))
                            };
                        i(this._element.querySelectorAll(".carousel-item img")).on(L.DRAG_START, function(t) {
                            return t.preventDefault()
                        }), this._pointerEvent ? (i(this._element).on(L.POINTERDOWN, function(t) {
                            return e(t)
                        }), i(this._element).on(L.POINTERUP, function(t) {
                            return n(t)
                        }), this._element.classList.add("pointer-event")) : (i(this._element).on(L.TOUCHSTART, function(t) {
                            return e(t)
                        }), i(this._element).on(L.TOUCHMOVE, function(e) {
                            var i;
                            (i = e).originalEvent.touches && 1 < i.originalEvent.touches.length ? t.touchDeltaX = 0 : t.touchDeltaX = i.originalEvent.touches[0].clientX - t.touchStartX
                        }), i(this._element).on(L.TOUCHEND, function(t) {
                            return n(t)
                        }))
                    }
                }, e._keydown = function(t) {
                    if (!/input|textarea/i.test(t.target.tagName)) switch (t.which) {
                        case 37:
                            t.preventDefault(), this.prev();
                            break;
                        case 39:
                            t.preventDefault(), this.next()
                    }
                }, e._getItemIndex = function(t) {
                    return this._items = t && t.parentNode ? [].slice.call(t.parentNode.querySelectorAll(".carousel-item")) : [], this._items.indexOf(t)
                }, e._getItemByDirection = function(t, e) {
                    var i = t === D,
                        n = t === N,
                        o = this._getItemIndex(e),
                        s = this._items.length - 1;
                    if ((n && 0 === o || i && o === s) && !this._config.wrap) return e;
                    var r = (o + (t === N ? -1 : 1)) % this._items.length;
                    return -1 === r ? this._items[this._items.length - 1] : this._items[r]
                }, e._triggerSlideEvent = function(t, e) {
                    var n = this._getItemIndex(t),
                        o = this._getItemIndex(this._element.querySelector(j)),
                        s = i.Event(L.SLIDE, {
                            relatedTarget: t,
                            direction: e,
                            from: o,
                            to: n
                        });
                    return i(this._element).trigger(s), s
                }, e._setActiveIndicatorElement = function(t) {
                    if (this._indicatorsElement) {
                        var e = [].slice.call(this._indicatorsElement.querySelectorAll(".active"));
                        i(e).removeClass(P);
                        var n = this._indicatorsElement.children[this._getItemIndex(t)];
                        n && i(n).addClass(P)
                    }
                }, e._slide = function(t, e) {
                    var n, o, s, r = this,
                        a = this._element.querySelector(j),
                        c = this._getItemIndex(a),
                        d = e || a && this._getItemByDirection(t, a),
                        u = this._getItemIndex(d),
                        f = Boolean(this._interval);
                    if (s = t === D ? (n = "carousel-item-left", o = "carousel-item-next", "left") : (n = "carousel-item-right", o = "carousel-item-prev", "right"), d && i(d).hasClass(P)) this._isSliding = !1;
                    else if (!this._triggerSlideEvent(d, s).isDefaultPrevented() && a && d) {
                        this._isSliding = !0, f && this.pause(), this._setActiveIndicatorElement(d);
                        var p = i.Event(L.SLID, {
                            relatedTarget: d,
                            direction: s,
                            from: c,
                            to: u
                        });
                        if (i(this._element).hasClass("slide")) {
                            i(d).addClass(o), l.reflow(d), i(a).addClass(n), i(d).addClass(n);
                            var h = parseInt(d.getAttribute("data-interval"), 10);
                            this._config.interval = h ? (this._config.defaultInterval = this._config.defaultInterval || this._config.interval, h) : this._config.defaultInterval || this._config.interval;
                            var m = l.getTransitionDurationFromElement(a);
                            i(a).one(l.TRANSITION_END, function() {
                                i(d).removeClass(n + " " + o).addClass(P), i(a).removeClass(P + " " + o + " " + n), r._isSliding = !1, setTimeout(function() {
                                    return i(r._element).trigger(p)
                                }, 0)
                            }).emulateTransitionEnd(m)
                        } else i(a).removeClass(P), i(d).addClass(P), this._isSliding = !1, i(this._element).trigger(p);
                        f && this.cycle()
                    }
                }, t._jQueryInterface = function(e) {
                    return this.each(function() {
                        var n = i(this).data(C),
                            o = s({}, I, i(this).data());
                        "object" == typeof e && (o = s({}, o, e));
                        var r = "string" == typeof e ? e : o.slide;
                        if (n || (n = new t(this, o), i(this).data(C, n)), "number" == typeof e) n.to(e);
                        else if ("string" == typeof r) {
                            if (void 0 === n[r]) throw new TypeError('No method named "' + r + '"');
                            n[r]()
                        } else o.interval && (n.pause(), n.cycle())
                    })
                }, t._dataApiClickHandler = function(e) {
                    var n = l.getSelectorFromElement(this);
                    if (n) {
                        var o = i(n)[0];
                        if (o && i(o).hasClass("carousel")) {
                            var r = s({}, i(o).data(), i(this).data()),
                                a = this.getAttribute("data-slide-to");
                            a && (r.interval = !1), t._jQueryInterface.call(i(o), r), a && i(o).data(C).to(a), e.preventDefault()
                        }
                    }
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }, {
                    key: "Default",
                    get: function() {
                        return I
                    }
                }]), t
            }();
        i(document).on(L.CLICK_DATA_API, "[data-slide], [data-slide-to]", M._dataApiClickHandler), i(window).on(L.LOAD_DATA_API, function() {
            for (var t = [].slice.call(document.querySelectorAll('[data-ride="carousel"]')), e = 0, n = t.length; e < n; e++) {
                var o = i(t[e]);
                M._jQueryInterface.call(o, o.data())
            }
        }), i.fn[k] = M._jQueryInterface, i.fn[k].Constructor = M, i.fn[k].noConflict = function() {
            return i.fn[k] = O, M._jQueryInterface
        };
        var F = "collapse",
            z = "bs.collapse",
            W = "." + z,
            q = i.fn[F],
            R = {
                toggle: !0,
                parent: ""
            },
            U = {
                toggle: "boolean",
                parent: "(string|element)"
            },
            Q = {
                SHOW: "show" + W,
                SHOWN: "shown" + W,
                HIDE: "hide" + W,
                HIDDEN: "hidden" + W,
                CLICK_DATA_API: "click" + W + ".data-api"
            },
            K = "show",
            Y = "collapse",
            V = "collapsing",
            X = "collapsed",
            G = '[data-toggle="collapse"]',
            Z = function() {
                function t(t, e) {
                    this._isTransitioning = !1, this._element = t, this._config = this._getConfig(e), this._triggerArray = [].slice.call(document.querySelectorAll('[data-toggle="collapse"][href="#' + t.id + '"],[data-toggle="collapse"][data-target="#' + t.id + '"]'));
                    for (var i = [].slice.call(document.querySelectorAll(G)), n = 0, o = i.length; n < o; n++) {
                        var s = i[n],
                            r = l.getSelectorFromElement(s),
                            a = [].slice.call(document.querySelectorAll(r)).filter(function(e) {
                                return e === t
                            });
                        null !== r && 0 < a.length && (this._selector = r, this._triggerArray.push(s))
                    }
                    this._parent = this._config.parent ? this._getParent() : null, this._config.parent || this._addAriaAndCollapsedClass(this._element, this._triggerArray), this._config.toggle && this.toggle()
                }
                var e = t.prototype;
                return e.toggle = function() {
                    i(this._element).hasClass(K) ? this.hide() : this.show()
                }, e.show = function() {
                    var e, n, o = this;
                    if (!(this._isTransitioning || i(this._element).hasClass(K) || (this._parent && 0 === (e = [].slice.call(this._parent.querySelectorAll(".show, .collapsing")).filter(function(t) {
                            return "string" == typeof o._config.parent ? t.getAttribute("data-parent") === o._config.parent : t.classList.contains(Y)
                        })).length && (e = null), e && (n = i(e).not(this._selector).data(z)) && n._isTransitioning))) {
                        var s = i.Event(Q.SHOW);
                        if (i(this._element).trigger(s), !s.isDefaultPrevented()) {
                            e && (t._jQueryInterface.call(i(e).not(this._selector), "hide"), n || i(e).data(z, null));
                            var r = this._getDimension();
                            i(this._element).removeClass(Y).addClass(V), this._element.style[r] = 0, this._triggerArray.length && i(this._triggerArray).removeClass(X).attr("aria-expanded", !0), this.setTransitioning(!0);
                            var a = "scroll" + (r[0].toUpperCase() + r.slice(1)),
                                c = l.getTransitionDurationFromElement(this._element);
                            i(this._element).one(l.TRANSITION_END, function() {
                                i(o._element).removeClass(V).addClass(Y).addClass(K), o._element.style[r] = "", o.setTransitioning(!1), i(o._element).trigger(Q.SHOWN)
                            }).emulateTransitionEnd(c), this._element.style[r] = this._element[a] + "px"
                        }
                    }
                }, e.hide = function() {
                    var t = this;
                    if (!this._isTransitioning && i(this._element).hasClass(K)) {
                        var e = i.Event(Q.HIDE);
                        if (i(this._element).trigger(e), !e.isDefaultPrevented()) {
                            var n = this._getDimension();
                            this._element.style[n] = this._element.getBoundingClientRect()[n] + "px", l.reflow(this._element), i(this._element).addClass(V).removeClass(Y).removeClass(K);
                            var o = this._triggerArray.length;
                            if (0 < o)
                                for (var s = 0; s < o; s++) {
                                    var r = this._triggerArray[s],
                                        a = l.getSelectorFromElement(r);
                                    null !== a && (i([].slice.call(document.querySelectorAll(a))).hasClass(K) || i(r).addClass(X).attr("aria-expanded", !1))
                                }
                            this.setTransitioning(!0), this._element.style[n] = "";
                            var c = l.getTransitionDurationFromElement(this._element);
                            i(this._element).one(l.TRANSITION_END, function() {
                                t.setTransitioning(!1), i(t._element).removeClass(V).addClass(Y).trigger(Q.HIDDEN)
                            }).emulateTransitionEnd(c)
                        }
                    }
                }, e.setTransitioning = function(t) {
                    this._isTransitioning = t
                }, e.dispose = function() {
                    i.removeData(this._element, z), this._config = null, this._parent = null, this._element = null, this._triggerArray = null, this._isTransitioning = null
                }, e._getConfig = function(t) {
                    return (t = s({}, R, t)).toggle = Boolean(t.toggle), l.typeCheckConfig(F, t, U), t
                }, e._getDimension = function() {
                    return i(this._element).hasClass("width") ? "width" : "height"
                }, e._getParent = function() {
                    var e, n = this;
                    l.isElement(this._config.parent) ? (e = this._config.parent, void 0 !== this._config.parent.jquery && (e = this._config.parent[0])) : e = document.querySelector(this._config.parent);
                    var o = '[data-toggle="collapse"][data-parent="' + this._config.parent + '"]',
                        s = [].slice.call(e.querySelectorAll(o));
                    return i(s).each(function(e, i) {
                        n._addAriaAndCollapsedClass(t._getTargetFromElement(i), [i])
                    }), e
                }, e._addAriaAndCollapsedClass = function(t, e) {
                    var n = i(t).hasClass(K);
                    e.length && i(e).toggleClass(X, !n).attr("aria-expanded", n)
                }, t._getTargetFromElement = function(t) {
                    var e = l.getSelectorFromElement(t);
                    return e ? document.querySelector(e) : null
                }, t._jQueryInterface = function(e) {
                    return this.each(function() {
                        var n = i(this),
                            o = n.data(z),
                            r = s({}, R, n.data(), "object" == typeof e && e ? e : {});
                        if (!o && r.toggle && /show|hide/.test(e) && (r.toggle = !1), o || (o = new t(this, r), n.data(z, o)), "string" == typeof e) {
                            if (void 0 === o[e]) throw new TypeError('No method named "' + e + '"');
                            o[e]()
                        }
                    })
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }, {
                    key: "Default",
                    get: function() {
                        return R
                    }
                }]), t
            }();
        i(document).on(Q.CLICK_DATA_API, G, function(t) {
            "A" === t.currentTarget.tagName && t.preventDefault();
            var e = i(this),
                n = l.getSelectorFromElement(this),
                o = [].slice.call(document.querySelectorAll(n));
            i(o).each(function() {
                var t = i(this),
                    n = t.data(z) ? "toggle" : e.data();
                Z._jQueryInterface.call(t, n)
            })
        }), i.fn[F] = Z._jQueryInterface, i.fn[F].Constructor = Z, i.fn[F].noConflict = function() {
            return i.fn[F] = q, Z._jQueryInterface
        };
        var J = "dropdown",
            tt = "bs.dropdown",
            et = "." + tt,
            it = ".data-api",
            nt = i.fn[J],
            ot = new RegExp("38|40|27"),
            st = {
                HIDE: "hide" + et,
                HIDDEN: "hidden" + et,
                SHOW: "show" + et,
                SHOWN: "shown" + et,
                CLICK: "click" + et,
                CLICK_DATA_API: "click" + et + it,
                KEYDOWN_DATA_API: "keydown" + et + it,
                KEYUP_DATA_API: "keyup" + et + it
            },
            rt = "disabled",
            at = "show",
            lt = "dropdown-menu-right",
            ct = '[data-toggle="dropdown"]',
            dt = ".dropdown-menu",
            ut = {
                offset: 0,
                flip: !0,
                boundary: "scrollParent",
                reference: "toggle",
                display: "dynamic"
            },
            ft = {
                offset: "(number|string|function)",
                flip: "boolean",
                boundary: "(string|element)",
                reference: "(string|element)",
                display: "string"
            },
            pt = function() {
                function t(t, e) {
                    this._element = t, this._popper = null, this._config = this._getConfig(e), this._menu = this._getMenuElement(), this._inNavbar = this._detectNavbar(), this._addEventListeners()
                }
                var n = t.prototype;
                return n.toggle = function() {
                    if (!this._element.disabled && !i(this._element).hasClass(rt)) {
                        var n = t._getParentFromElement(this._element),
                            o = i(this._menu).hasClass(at);
                        if (t._clearMenus(), !o) {
                            var s = {
                                    relatedTarget: this._element
                                },
                                r = i.Event(st.SHOW, s);
                            if (i(n).trigger(r), !r.isDefaultPrevented()) {
                                if (!this._inNavbar) {
                                    if (void 0 === e) throw new TypeError("Bootstrap's dropdowns require Popper.js (https://popper.js.org/)");
                                    var a = this._element;
                                    "parent" === this._config.reference ? a = n : l.isElement(this._config.reference) && (a = this._config.reference, void 0 !== this._config.reference.jquery && (a = this._config.reference[0])), "scrollParent" !== this._config.boundary && i(n).addClass("position-static"), this._popper = new e(a, this._menu, this._getPopperConfig())
                                }
                                "ontouchstart" in document.documentElement && 0 === i(n).closest(".navbar-nav").length && i(document.body).children().on("mouseover", null, i.noop), this._element.focus(), this._element.setAttribute("aria-expanded", !0), i(this._menu).toggleClass(at), i(n).toggleClass(at).trigger(i.Event(st.SHOWN, s))
                            }
                        }
                    }
                }, n.show = function() {
                    if (!(this._element.disabled || i(this._element).hasClass(rt) || i(this._menu).hasClass(at))) {
                        var e = {
                                relatedTarget: this._element
                            },
                            n = i.Event(st.SHOW, e),
                            o = t._getParentFromElement(this._element);
                        i(o).trigger(n), n.isDefaultPrevented() || (i(this._menu).toggleClass(at), i(o).toggleClass(at).trigger(i.Event(st.SHOWN, e)))
                    }
                }, n.hide = function() {
                    if (!this._element.disabled && !i(this._element).hasClass(rt) && i(this._menu).hasClass(at)) {
                        var e = {
                                relatedTarget: this._element
                            },
                            n = i.Event(st.HIDE, e),
                            o = t._getParentFromElement(this._element);
                        i(o).trigger(n), n.isDefaultPrevented() || (i(this._menu).toggleClass(at), i(o).toggleClass(at).trigger(i.Event(st.HIDDEN, e)))
                    }
                }, n.dispose = function() {
                    i.removeData(this._element, tt), i(this._element).off(et), this._element = null, (this._menu = null) !== this._popper && (this._popper.destroy(), this._popper = null)
                }, n.update = function() {
                    this._inNavbar = this._detectNavbar(), null !== this._popper && this._popper.scheduleUpdate()
                }, n._addEventListeners = function() {
                    var t = this;
                    i(this._element).on(st.CLICK, function(e) {
                        e.preventDefault(), e.stopPropagation(), t.toggle()
                    })
                }, n._getConfig = function(t) {
                    return t = s({}, this.constructor.Default, i(this._element).data(), t), l.typeCheckConfig(J, t, this.constructor.DefaultType), t
                }, n._getMenuElement = function() {
                    if (!this._menu) {
                        var e = t._getParentFromElement(this._element);
                        e && (this._menu = e.querySelector(dt))
                    }
                    return this._menu
                }, n._getPlacement = function() {
                    var t = i(this._element.parentNode),
                        e = "bottom-start";
                    return t.hasClass("dropup") ? (e = "top-start", i(this._menu).hasClass(lt) && (e = "top-end")) : t.hasClass("dropright") ? e = "right-start" : t.hasClass("dropleft") ? e = "left-start" : i(this._menu).hasClass(lt) && (e = "bottom-end"), e
                }, n._detectNavbar = function() {
                    return 0 < i(this._element).closest(".navbar").length
                }, n._getPopperConfig = function() {
                    var t = this,
                        e = {};
                    "function" == typeof this._config.offset ? e.fn = function(e) {
                        return e.offsets = s({}, e.offsets, t._config.offset(e.offsets) || {}), e
                    } : e.offset = this._config.offset;
                    var i = {
                        placement: this._getPlacement(),
                        modifiers: {
                            offset: e,
                            flip: {
                                enabled: this._config.flip
                            },
                            preventOverflow: {
                                boundariesElement: this._config.boundary
                            }
                        }
                    };
                    return "static" === this._config.display && (i.modifiers.applyStyle = {
                        enabled: !1
                    }), i
                }, t._jQueryInterface = function(e) {
                    return this.each(function() {
                        var n = i(this).data(tt);
                        if (n || (n = new t(this, "object" == typeof e ? e : null), i(this).data(tt, n)), "string" == typeof e) {
                            if (void 0 === n[e]) throw new TypeError('No method named "' + e + '"');
                            n[e]()
                        }
                    })
                }, t._clearMenus = function(e) {
                    if (!e || 3 !== e.which && ("keyup" !== e.type || 9 === e.which))
                        for (var n = [].slice.call(document.querySelectorAll(ct)), o = 0, s = n.length; o < s; o++) {
                            var r = t._getParentFromElement(n[o]),
                                a = i(n[o]).data(tt),
                                l = {
                                    relatedTarget: n[o]
                                };
                            if (e && "click" === e.type && (l.clickEvent = e), a) {
                                var c = a._menu;
                                if (i(r).hasClass(at) && !(e && ("click" === e.type && /input|textarea/i.test(e.target.tagName) || "keyup" === e.type && 9 === e.which) && i.contains(r, e.target))) {
                                    var d = i.Event(st.HIDE, l);
                                    i(r).trigger(d), d.isDefaultPrevented() || ("ontouchstart" in document.documentElement && i(document.body).children().off("mouseover", null, i.noop), n[o].setAttribute("aria-expanded", "false"), i(c).removeClass(at), i(r).removeClass(at).trigger(i.Event(st.HIDDEN, l)))
                                }
                            }
                        }
                }, t._getParentFromElement = function(t) {
                    var e, i = l.getSelectorFromElement(t);
                    return i && (e = document.querySelector(i)), e || t.parentNode
                }, t._dataApiKeydownHandler = function(e) {
                    if ((/input|textarea/i.test(e.target.tagName) ? !(32 === e.which || 27 !== e.which && (40 !== e.which && 38 !== e.which || i(e.target).closest(dt).length)) : ot.test(e.which)) && (e.preventDefault(), e.stopPropagation(), !this.disabled && !i(this).hasClass(rt))) {
                        var n = t._getParentFromElement(this),
                            o = i(n).hasClass(at);
                        if (o && (!o || 27 !== e.which && 32 !== e.which)) {
                            var s = [].slice.call(n.querySelectorAll(".dropdown-menu .dropdown-item:not(.disabled):not(:disabled)"));
                            if (0 !== s.length) {
                                var r = s.indexOf(e.target);
                                38 === e.which && 0 < r && r--, 40 === e.which && r < s.length - 1 && r++, r < 0 && (r = 0), s[r].focus()
                            }
                        } else {
                            if (27 === e.which) {
                                var a = n.querySelector(ct);
                                i(a).trigger("focus")
                            }
                            i(this).trigger("click")
                        }
                    }
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }, {
                    key: "Default",
                    get: function() {
                        return ut
                    }
                }, {
                    key: "DefaultType",
                    get: function() {
                        return ft
                    }
                }]), t
            }();
        i(document).on(st.KEYDOWN_DATA_API, ct, pt._dataApiKeydownHandler).on(st.KEYDOWN_DATA_API, dt, pt._dataApiKeydownHandler).on(st.CLICK_DATA_API + " " + st.KEYUP_DATA_API, pt._clearMenus).on(st.CLICK_DATA_API, ct, function(t) {
            t.preventDefault(), t.stopPropagation(), pt._jQueryInterface.call(i(this), "toggle")
        }).on(st.CLICK_DATA_API, ".dropdown form", function(t) {
            t.stopPropagation()
        }), i.fn[J] = pt._jQueryInterface, i.fn[J].Constructor = pt, i.fn[J].noConflict = function() {
            return i.fn[J] = nt, pt._jQueryInterface
        };
        var ht = "modal",
            mt = "bs.modal",
            gt = "." + mt,
            vt = i.fn[ht],
            yt = {
                backdrop: !0,
                keyboard: !0,
                focus: !0,
                show: !0
            },
            wt = {
                backdrop: "(boolean|string)",
                keyboard: "boolean",
                focus: "boolean",
                show: "boolean"
            },
            _t = {
                HIDE: "hide" + gt,
                HIDDEN: "hidden" + gt,
                SHOW: "show" + gt,
                SHOWN: "shown" + gt,
                FOCUSIN: "focusin" + gt,
                RESIZE: "resize" + gt,
                CLICK_DISMISS: "click.dismiss" + gt,
                KEYDOWN_DISMISS: "keydown.dismiss" + gt,
                MOUSEUP_DISMISS: "mouseup.dismiss" + gt,
                MOUSEDOWN_DISMISS: "mousedown.dismiss" + gt,
                CLICK_DATA_API: "click" + gt + ".data-api"
            },
            bt = "modal-open",
            St = "fade",
            xt = "show",
            Tt = ".modal-dialog",
            kt = ".fixed-top, .fixed-bottom, .is-fixed, .sticky-top",
            Ct = ".sticky-top",
            Et = function() {
                function t(t, e) {
                    this._config = this._getConfig(e), this._element = t, this._dialog = t.querySelector(Tt), this._backdrop = null, this._isShown = !1, this._isBodyOverflowing = !1, this._ignoreBackdropClick = !1, this._isTransitioning = !1, this._scrollbarWidth = 0
                }
                var e = t.prototype;
                return e.toggle = function(t) {
                    return this._isShown ? this.hide() : this.show(t)
                }, e.show = function(t) {
                    var e = this;
                    if (!this._isShown && !this._isTransitioning) {
                        i(this._element).hasClass(St) && (this._isTransitioning = !0);
                        var n = i.Event(_t.SHOW, {
                            relatedTarget: t
                        });
                        i(this._element).trigger(n), this._isShown || n.isDefaultPrevented() || (this._isShown = !0, this._checkScrollbar(), this._setScrollbar(), this._adjustDialog(), this._setEscapeEvent(), this._setResizeEvent(), i(this._element).on(_t.CLICK_DISMISS, '[data-dismiss="modal"]', function(t) {
                            return e.hide(t)
                        }), i(this._dialog).on(_t.MOUSEDOWN_DISMISS, function() {
                            i(e._element).one(_t.MOUSEUP_DISMISS, function(t) {
                                i(t.target).is(e._element) && (e._ignoreBackdropClick = !0)
                            })
                        }), this._showBackdrop(function() {
                            return e._showElement(t)
                        }))
                    }
                }, e.hide = function(t) {
                    var e = this;
                    if (t && t.preventDefault(), this._isShown && !this._isTransitioning) {
                        var n = i.Event(_t.HIDE);
                        if (i(this._element).trigger(n), this._isShown && !n.isDefaultPrevented()) {
                            this._isShown = !1;
                            var o = i(this._element).hasClass(St);
                            if (o && (this._isTransitioning = !0), this._setEscapeEvent(), this._setResizeEvent(), i(document).off(_t.FOCUSIN), i(this._element).removeClass(xt), i(this._element).off(_t.CLICK_DISMISS), i(this._dialog).off(_t.MOUSEDOWN_DISMISS), o) {
                                var s = l.getTransitionDurationFromElement(this._element);
                                i(this._element).one(l.TRANSITION_END, function(t) {
                                    return e._hideModal(t)
                                }).emulateTransitionEnd(s)
                            } else this._hideModal()
                        }
                    }
                }, e.dispose = function() {
                    [window, this._element, this._dialog].forEach(function(t) {
                        return i(t).off(gt)
                    }), i(document).off(_t.FOCUSIN), i.removeData(this._element, mt), this._config = null, this._element = null, this._dialog = null, this._backdrop = null, this._isShown = null, this._isBodyOverflowing = null, this._ignoreBackdropClick = null, this._isTransitioning = null, this._scrollbarWidth = null
                }, e.handleUpdate = function() {
                    this._adjustDialog()
                }, e._getConfig = function(t) {
                    return t = s({}, yt, t), l.typeCheckConfig(ht, t, wt), t
                }, e._showElement = function(t) {
                    var e = this,
                        n = i(this._element).hasClass(St);
                    this._element.parentNode && this._element.parentNode.nodeType === Node.ELEMENT_NODE || document.body.appendChild(this._element), this._element.style.display = "block", this._element.removeAttribute("aria-hidden"), this._element.setAttribute("aria-modal", !0), this._element.scrollTop = 0, n && l.reflow(this._element), i(this._element).addClass(xt), this._config.focus && this._enforceFocus();
                    var o = i.Event(_t.SHOWN, {
                            relatedTarget: t
                        }),
                        s = function() {
                            e._config.focus && e._element.focus(), e._isTransitioning = !1, i(e._element).trigger(o)
                        };
                    if (n) {
                        var r = l.getTransitionDurationFromElement(this._dialog);
                        i(this._dialog).one(l.TRANSITION_END, s).emulateTransitionEnd(r)
                    } else s()
                }, e._enforceFocus = function() {
                    var t = this;
                    i(document).off(_t.FOCUSIN).on(_t.FOCUSIN, function(e) {
                        document !== e.target && t._element !== e.target && 0 === i(t._element).has(e.target).length && t._element.focus()
                    })
                }, e._setEscapeEvent = function() {
                    var t = this;
                    this._isShown && this._config.keyboard ? i(this._element).on(_t.KEYDOWN_DISMISS, function(e) {
                        27 === e.which && (e.preventDefault(), t.hide())
                    }) : this._isShown || i(this._element).off(_t.KEYDOWN_DISMISS)
                }, e._setResizeEvent = function() {
                    var t = this;
                    this._isShown ? i(window).on(_t.RESIZE, function(e) {
                        return t.handleUpdate(e)
                    }) : i(window).off(_t.RESIZE)
                }, e._hideModal = function() {
                    var t = this;
                    this._element.style.display = "none", this._element.setAttribute("aria-hidden", !0), this._element.removeAttribute("aria-modal"), this._isTransitioning = !1, this._showBackdrop(function() {
                        i(document.body).removeClass(bt), t._resetAdjustments(), t._resetScrollbar(), i(t._element).trigger(_t.HIDDEN)
                    })
                }, e._removeBackdrop = function() {
                    this._backdrop && (i(this._backdrop).remove(), this._backdrop = null)
                }, e._showBackdrop = function(t) {
                    var e = this,
                        n = i(this._element).hasClass(St) ? St : "";
                    if (this._isShown && this._config.backdrop) {
                        if (this._backdrop = document.createElement("div"), this._backdrop.className = "modal-backdrop", n && this._backdrop.classList.add(n), i(this._backdrop).appendTo(document.body), i(this._element).on(_t.CLICK_DISMISS, function(t) {
                                e._ignoreBackdropClick ? e._ignoreBackdropClick = !1 : t.target === t.currentTarget && ("static" === e._config.backdrop ? e._element.focus() : e.hide())
                            }), n && l.reflow(this._backdrop), i(this._backdrop).addClass(xt), !t) return;
                        if (!n) return void t();
                        var o = l.getTransitionDurationFromElement(this._backdrop);
                        i(this._backdrop).one(l.TRANSITION_END, t).emulateTransitionEnd(o)
                    } else if (!this._isShown && this._backdrop) {
                        i(this._backdrop).removeClass(xt);
                        var s = function() {
                            e._removeBackdrop(), t && t()
                        };
                        if (i(this._element).hasClass(St)) {
                            var r = l.getTransitionDurationFromElement(this._backdrop);
                            i(this._backdrop).one(l.TRANSITION_END, s).emulateTransitionEnd(r)
                        } else s()
                    } else t && t()
                }, e._adjustDialog = function() {
                    var t = this._element.scrollHeight > document.documentElement.clientHeight;
                    !this._isBodyOverflowing && t && (this._element.style.paddingLeft = this._scrollbarWidth + "px"), this._isBodyOverflowing && !t && (this._element.style.paddingRight = this._scrollbarWidth + "px")
                }, e._resetAdjustments = function() {
                    this._element.style.paddingLeft = "", this._element.style.paddingRight = ""
                }, e._checkScrollbar = function() {
                    var t = document.body.getBoundingClientRect();
                    this._isBodyOverflowing = t.left + t.right < window.innerWidth, this._scrollbarWidth = this._getScrollbarWidth()
                }, e._setScrollbar = function() {
                    var t = this;
                    if (this._isBodyOverflowing) {
                        var e = [].slice.call(document.querySelectorAll(kt)),
                            n = [].slice.call(document.querySelectorAll(Ct));
                        i(e).each(function(e, n) {
                            var o = n.style.paddingRight,
                                s = i(n).css("padding-right");
                            i(n).data("padding-right", o).css("padding-right", parseFloat(s) + t._scrollbarWidth + "px")
                        }), i(n).each(function(e, n) {
                            var o = n.style.marginRight,
                                s = i(n).css("margin-right");
                            i(n).data("margin-right", o).css("margin-right", parseFloat(s) - t._scrollbarWidth + "px")
                        });
                        var o = document.body.style.paddingRight,
                            s = i(document.body).css("padding-right");
                        i(document.body).data("padding-right", o).css("padding-right", parseFloat(s) + this._scrollbarWidth + "px")
                    }
                    i(document.body).addClass(bt)
                }, e._resetScrollbar = function() {
                    var t = [].slice.call(document.querySelectorAll(kt));
                    i(t).each(function(t, e) {
                        var n = i(e).data("padding-right");
                        i(e).removeData("padding-right"), e.style.paddingRight = n || ""
                    });
                    var e = [].slice.call(document.querySelectorAll("" + Ct));
                    i(e).each(function(t, e) {
                        var n = i(e).data("margin-right");
                        void 0 !== n && i(e).css("margin-right", n).removeData("margin-right")
                    });
                    var n = i(document.body).data("padding-right");
                    i(document.body).removeData("padding-right"), document.body.style.paddingRight = n || ""
                }, e._getScrollbarWidth = function() {
                    var t = document.createElement("div");
                    t.className = "modal-scrollbar-measure", document.body.appendChild(t);
                    var e = t.getBoundingClientRect().width - t.clientWidth;
                    return document.body.removeChild(t), e
                }, t._jQueryInterface = function(e, n) {
                    return this.each(function() {
                        var o = i(this).data(mt),
                            r = s({}, yt, i(this).data(), "object" == typeof e && e ? e : {});
                        if (o || (o = new t(this, r), i(this).data(mt, o)), "string" == typeof e) {
                            if (void 0 === o[e]) throw new TypeError('No method named "' + e + '"');
                            o[e](n)
                        } else r.show && o.show(n)
                    })
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }, {
                    key: "Default",
                    get: function() {
                        return yt
                    }
                }]), t
            }();
        i(document).on(_t.CLICK_DATA_API, '[data-toggle="modal"]', function(t) {
            var e, n = this,
                o = l.getSelectorFromElement(this);
            o && (e = document.querySelector(o));
            var r = i(e).data(mt) ? "toggle" : s({}, i(e).data(), i(this).data());
            "A" !== this.tagName && "AREA" !== this.tagName || t.preventDefault();
            var a = i(e).one(_t.SHOW, function(t) {
                t.isDefaultPrevented() || a.one(_t.HIDDEN, function() {
                    i(n).is(":visible") && n.focus()
                })
            });
            Et._jQueryInterface.call(i(e), r, this)
        }), i.fn[ht] = Et._jQueryInterface, i.fn[ht].Constructor = Et, i.fn[ht].noConflict = function() {
            return i.fn[ht] = vt, Et._jQueryInterface
        };
        var At = "tooltip",
            Ot = "bs.tooltip",
            It = "." + Ot,
            $t = i.fn[At],
            Dt = "bs-tooltip",
            Nt = new RegExp("(^|\\s)" + Dt + "\\S+", "g"),
            Lt = {
                animation: "boolean",
                template: "string",
                title: "(string|element|function)",
                trigger: "string",
                delay: "(number|object)",
                html: "boolean",
                selector: "(string|boolean)",
                placement: "(string|function)",
                offset: "(number|string)",
                container: "(string|element|boolean)",
                fallbackPlacement: "(string|array)",
                boundary: "(string|element)"
            },
            Pt = {
                AUTO: "auto",
                TOP: "top",
                RIGHT: "right",
                BOTTOM: "bottom",
                LEFT: "left"
            },
            jt = {
                animation: !0,
                template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
                trigger: "hover focus",
                title: "",
                delay: 0,
                html: !1,
                selector: !1,
                placement: "top",
                offset: 0,
                container: !1,
                fallbackPlacement: "flip",
                boundary: "scrollParent"
            },
            Ht = "show",
            Bt = {
                HIDE: "hide" + It,
                HIDDEN: "hidden" + It,
                SHOW: "show" + It,
                SHOWN: "shown" + It,
                INSERTED: "inserted" + It,
                CLICK: "click" + It,
                FOCUSIN: "focusin" + It,
                FOCUSOUT: "focusout" + It,
                MOUSEENTER: "mouseenter" + It,
                MOUSELEAVE: "mouseleave" + It
            },
            Mt = "fade",
            Ft = "show",
            zt = "hover",
            Wt = "focus",
            qt = function() {
                function t(t, i) {
                    if (void 0 === e) throw new TypeError("Bootstrap's tooltips require Popper.js (https://popper.js.org/)");
                    this._isEnabled = !0, this._timeout = 0, this._hoverState = "", this._activeTrigger = {}, this._popper = null, this.element = t, this.config = this._getConfig(i), this.tip = null, this._setListeners()
                }
                var n = t.prototype;
                return n.enable = function() {
                    this._isEnabled = !0
                }, n.disable = function() {
                    this._isEnabled = !1
                }, n.toggleEnabled = function() {
                    this._isEnabled = !this._isEnabled
                }, n.toggle = function(t) {
                    if (this._isEnabled)
                        if (t) {
                            var e = this.constructor.DATA_KEY,
                                n = i(t.currentTarget).data(e);
                            n || (n = new this.constructor(t.currentTarget, this._getDelegateConfig()), i(t.currentTarget).data(e, n)), n._activeTrigger.click = !n._activeTrigger.click, n._isWithActiveTrigger() ? n._enter(null, n) : n._leave(null, n)
                        } else {
                            if (i(this.getTipElement()).hasClass(Ft)) return void this._leave(null, this);
                            this._enter(null, this)
                        }
                }, n.dispose = function() {
                    clearTimeout(this._timeout), i.removeData(this.element, this.constructor.DATA_KEY), i(this.element).off(this.constructor.EVENT_KEY), i(this.element).closest(".modal").off("hide.bs.modal"), this.tip && i(this.tip).remove(), this._isEnabled = null, this._timeout = null, this._hoverState = null, (this._activeTrigger = null) !== this._popper && this._popper.destroy(), this._popper = null, this.element = null, this.config = null, this.tip = null
                }, n.show = function() {
                    var t = this;
                    if ("none" === i(this.element).css("display")) throw new Error("Please use show on visible elements");
                    var n = i.Event(this.constructor.Event.SHOW);
                    if (this.isWithContent() && this._isEnabled) {
                        i(this.element).trigger(n);
                        var o = l.findShadowRoot(this.element),
                            s = i.contains(null !== o ? o : this.element.ownerDocument.documentElement, this.element);
                        if (n.isDefaultPrevented() || !s) return;
                        var r = this.getTipElement(),
                            a = l.getUID(this.constructor.NAME);
                        r.setAttribute("id", a), this.element.setAttribute("aria-describedby", a), this.setContent(), this.config.animation && i(r).addClass(Mt);
                        var c = "function" == typeof this.config.placement ? this.config.placement.call(this, r, this.element) : this.config.placement,
                            d = this._getAttachment(c);
                        this.addAttachmentClass(d);
                        var u = this._getContainer();
                        i(r).data(this.constructor.DATA_KEY, this), i.contains(this.element.ownerDocument.documentElement, this.tip) || i(r).appendTo(u), i(this.element).trigger(this.constructor.Event.INSERTED), this._popper = new e(this.element, r, {
                            placement: d,
                            modifiers: {
                                offset: {
                                    offset: this.config.offset
                                },
                                flip: {
                                    behavior: this.config.fallbackPlacement
                                },
                                arrow: {
                                    element: ".arrow"
                                },
                                preventOverflow: {
                                    boundariesElement: this.config.boundary
                                }
                            },
                            onCreate: function(e) {
                                e.originalPlacement !== e.placement && t._handlePopperPlacementChange(e)
                            },
                            onUpdate: function(e) {
                                return t._handlePopperPlacementChange(e)
                            }
                        }), i(r).addClass(Ft), "ontouchstart" in document.documentElement && i(document.body).children().on("mouseover", null, i.noop);
                        var f = function() {
                            t.config.animation && t._fixTransition();
                            var e = t._hoverState;
                            t._hoverState = null, i(t.element).trigger(t.constructor.Event.SHOWN), "out" === e && t._leave(null, t)
                        };
                        if (i(this.tip).hasClass(Mt)) {
                            var p = l.getTransitionDurationFromElement(this.tip);
                            i(this.tip).one(l.TRANSITION_END, f).emulateTransitionEnd(p)
                        } else f()
                    }
                }, n.hide = function(t) {
                    var e = this,
                        n = this.getTipElement(),
                        o = i.Event(this.constructor.Event.HIDE),
                        s = function() {
                            e._hoverState !== Ht && n.parentNode && n.parentNode.removeChild(n), e._cleanTipClass(), e.element.removeAttribute("aria-describedby"), i(e.element).trigger(e.constructor.Event.HIDDEN), null !== e._popper && e._popper.destroy(), t && t()
                        };
                    if (i(this.element).trigger(o), !o.isDefaultPrevented()) {
                        if (i(n).removeClass(Ft), "ontouchstart" in document.documentElement && i(document.body).children().off("mouseover", null, i.noop), this._activeTrigger.click = !1, this._activeTrigger[Wt] = !1, this._activeTrigger[zt] = !1, i(this.tip).hasClass(Mt)) {
                            var r = l.getTransitionDurationFromElement(n);
                            i(n).one(l.TRANSITION_END, s).emulateTransitionEnd(r)
                        } else s();
                        this._hoverState = ""
                    }
                }, n.update = function() {
                    null !== this._popper && this._popper.scheduleUpdate()
                }, n.isWithContent = function() {
                    return Boolean(this.getTitle())
                }, n.addAttachmentClass = function(t) {
                    i(this.getTipElement()).addClass(Dt + "-" + t)
                }, n.getTipElement = function() {
                    return this.tip = this.tip || i(this.config.template)[0], this.tip
                }, n.setContent = function() {
                    var t = this.getTipElement();
                    this.setElementContent(i(t.querySelectorAll(".tooltip-inner")), this.getTitle()), i(t).removeClass(Mt + " " + Ft)
                }, n.setElementContent = function(t, e) {
                    var n = this.config.html;
                    "object" == typeof e && (e.nodeType || e.jquery) ? n ? i(e).parent().is(t) || t.empty().append(e) : t.text(i(e).text()) : t[n ? "html" : "text"](e)
                }, n.getTitle = function() {
                    var t = this.element.getAttribute("data-original-title");
                    return t || (t = "function" == typeof this.config.title ? this.config.title.call(this.element) : this.config.title), t
                }, n._getContainer = function() {
                    return !1 === this.config.container ? document.body : l.isElement(this.config.container) ? i(this.config.container) : i(document).find(this.config.container)
                }, n._getAttachment = function(t) {
                    return Pt[t.toUpperCase()]
                }, n._setListeners = function() {
                    var t = this;
                    this.config.trigger.split(" ").forEach(function(e) {
                        if ("click" === e) i(t.element).on(t.constructor.Event.CLICK, t.config.selector, function(e) {
                            return t.toggle(e)
                        });
                        else if ("manual" !== e) {
                            var n = e === zt ? t.constructor.Event.MOUSEENTER : t.constructor.Event.FOCUSIN,
                                o = e === zt ? t.constructor.Event.MOUSELEAVE : t.constructor.Event.FOCUSOUT;
                            i(t.element).on(n, t.config.selector, function(e) {
                                return t._enter(e)
                            }).on(o, t.config.selector, function(e) {
                                return t._leave(e)
                            })
                        }
                    }), i(this.element).closest(".modal").on("hide.bs.modal", function() {
                        t.element && t.hide()
                    }), this.config.selector ? this.config = s({}, this.config, {
                        trigger: "manual",
                        selector: ""
                    }) : this._fixTitle()
                }, n._fixTitle = function() {
                    var t = typeof this.element.getAttribute("data-original-title");
                    (this.element.getAttribute("title") || "string" !== t) && (this.element.setAttribute("data-original-title", this.element.getAttribute("title") || ""), this.element.setAttribute("title", ""))
                }, n._enter = function(t, e) {
                    var n = this.constructor.DATA_KEY;
                    (e = e || i(t.currentTarget).data(n)) || (e = new this.constructor(t.currentTarget, this._getDelegateConfig()), i(t.currentTarget).data(n, e)), t && (e._activeTrigger["focusin" === t.type ? Wt : zt] = !0), i(e.getTipElement()).hasClass(Ft) || e._hoverState === Ht ? e._hoverState = Ht : (clearTimeout(e._timeout), e._hoverState = Ht, e.config.delay && e.config.delay.show ? e._timeout = setTimeout(function() {
                        e._hoverState === Ht && e.show()
                    }, e.config.delay.show) : e.show())
                }, n._leave = function(t, e) {
                    var n = this.constructor.DATA_KEY;
                    (e = e || i(t.currentTarget).data(n)) || (e = new this.constructor(t.currentTarget, this._getDelegateConfig()), i(t.currentTarget).data(n, e)), t && (e._activeTrigger["focusout" === t.type ? Wt : zt] = !1), e._isWithActiveTrigger() || (clearTimeout(e._timeout), e._hoverState = "out", e.config.delay && e.config.delay.hide ? e._timeout = setTimeout(function() {
                        "out" === e._hoverState && e.hide()
                    }, e.config.delay.hide) : e.hide())
                }, n._isWithActiveTrigger = function() {
                    for (var t in this._activeTrigger)
                        if (this._activeTrigger[t]) return !0;
                    return !1
                }, n._getConfig = function(t) {
                    return "number" == typeof(t = s({}, this.constructor.Default, i(this.element).data(), "object" == typeof t && t ? t : {})).delay && (t.delay = {
                        show: t.delay,
                        hide: t.delay
                    }), "number" == typeof t.title && (t.title = t.title.toString()), "number" == typeof t.content && (t.content = t.content.toString()), l.typeCheckConfig(At, t, this.constructor.DefaultType), t
                }, n._getDelegateConfig = function() {
                    var t = {};
                    if (this.config)
                        for (var e in this.config) this.constructor.Default[e] !== this.config[e] && (t[e] = this.config[e]);
                    return t
                }, n._cleanTipClass = function() {
                    var t = i(this.getTipElement()),
                        e = t.attr("class").match(Nt);
                    null !== e && e.length && t.removeClass(e.join(""))
                }, n._handlePopperPlacementChange = function(t) {
                    var e = t.instance;
                    this.tip = e.popper, this._cleanTipClass(), this.addAttachmentClass(this._getAttachment(t.placement))
                }, n._fixTransition = function() {
                    var t = this.getTipElement(),
                        e = this.config.animation;
                    null === t.getAttribute("x-placement") && (i(t).removeClass(Mt), this.config.animation = !1, this.hide(), this.show(), this.config.animation = e)
                }, t._jQueryInterface = function(e) {
                    return this.each(function() {
                        var n = i(this).data(Ot),
                            o = "object" == typeof e && e;
                        if ((n || !/dispose|hide/.test(e)) && (n || (n = new t(this, o), i(this).data(Ot, n)), "string" == typeof e)) {
                            if (void 0 === n[e]) throw new TypeError('No method named "' + e + '"');
                            n[e]()
                        }
                    })
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }, {
                    key: "Default",
                    get: function() {
                        return jt
                    }
                }, {
                    key: "NAME",
                    get: function() {
                        return At
                    }
                }, {
                    key: "DATA_KEY",
                    get: function() {
                        return Ot
                    }
                }, {
                    key: "Event",
                    get: function() {
                        return Bt
                    }
                }, {
                    key: "EVENT_KEY",
                    get: function() {
                        return It
                    }
                }, {
                    key: "DefaultType",
                    get: function() {
                        return Lt
                    }
                }]), t
            }();
        i.fn[At] = qt._jQueryInterface, i.fn[At].Constructor = qt, i.fn[At].noConflict = function() {
            return i.fn[At] = $t, qt._jQueryInterface
        };
        var Rt = "popover",
            Ut = "bs.popover",
            Qt = "." + Ut,
            Kt = i.fn[Rt],
            Yt = "bs-popover",
            Vt = new RegExp("(^|\\s)" + Yt + "\\S+", "g"),
            Xt = s({}, qt.Default, {
                placement: "right",
                trigger: "click",
                content: "",
                template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'
            }),
            Gt = s({}, qt.DefaultType, {
                content: "(string|element|function)"
            }),
            Zt = {
                HIDE: "hide" + Qt,
                HIDDEN: "hidden" + Qt,
                SHOW: "show" + Qt,
                SHOWN: "shown" + Qt,
                INSERTED: "inserted" + Qt,
                CLICK: "click" + Qt,
                FOCUSIN: "focusin" + Qt,
                FOCUSOUT: "focusout" + Qt,
                MOUSEENTER: "mouseenter" + Qt,
                MOUSELEAVE: "mouseleave" + Qt
            },
            Jt = function(t) {
                function e() {
                    return t.apply(this, arguments) || this
                }
                var n, s;
                s = t, (n = e).prototype = Object.create(s.prototype), (n.prototype.constructor = n).__proto__ = s;
                var r = e.prototype;
                return r.isWithContent = function() {
                    return this.getTitle() || this._getContent()
                }, r.addAttachmentClass = function(t) {
                    i(this.getTipElement()).addClass(Yt + "-" + t)
                }, r.getTipElement = function() {
                    return this.tip = this.tip || i(this.config.template)[0], this.tip
                }, r.setContent = function() {
                    var t = i(this.getTipElement());
                    this.setElementContent(t.find(".popover-header"), this.getTitle());
                    var e = this._getContent();
                    "function" == typeof e && (e = e.call(this.element)), this.setElementContent(t.find(".popover-body"), e), t.removeClass("fade show")
                }, r._getContent = function() {
                    return this.element.getAttribute("data-content") || this.config.content
                }, r._cleanTipClass = function() {
                    var t = i(this.getTipElement()),
                        e = t.attr("class").match(Vt);
                    null !== e && 0 < e.length && t.removeClass(e.join(""))
                }, e._jQueryInterface = function(t) {
                    return this.each(function() {
                        var n = i(this).data(Ut),
                            o = "object" == typeof t ? t : null;
                        if ((n || !/dispose|hide/.test(t)) && (n || (n = new e(this, o), i(this).data(Ut, n)), "string" == typeof t)) {
                            if (void 0 === n[t]) throw new TypeError('No method named "' + t + '"');
                            n[t]()
                        }
                    })
                }, o(e, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }, {
                    key: "Default",
                    get: function() {
                        return Xt
                    }
                }, {
                    key: "NAME",
                    get: function() {
                        return Rt
                    }
                }, {
                    key: "DATA_KEY",
                    get: function() {
                        return Ut
                    }
                }, {
                    key: "Event",
                    get: function() {
                        return Zt
                    }
                }, {
                    key: "EVENT_KEY",
                    get: function() {
                        return Qt
                    }
                }, {
                    key: "DefaultType",
                    get: function() {
                        return Gt
                    }
                }]), e
            }(qt);
        i.fn[Rt] = Jt._jQueryInterface, i.fn[Rt].Constructor = Jt, i.fn[Rt].noConflict = function() {
            return i.fn[Rt] = Kt, Jt._jQueryInterface
        };
        var te = "scrollspy",
            ee = "bs.scrollspy",
            ie = "." + ee,
            ne = i.fn[te],
            oe = {
                offset: 10,
                method: "auto",
                target: ""
            },
            se = {
                offset: "number",
                method: "string",
                target: "(string|element)"
            },
            re = {
                ACTIVATE: "activate" + ie,
                SCROLL: "scroll" + ie,
                LOAD_DATA_API: "load" + ie + ".data-api"
            },
            ae = "active",
            le = ".nav, .list-group",
            ce = ".nav-link",
            de = ".list-group-item",
            ue = ".dropdown-item",
            fe = "position",
            pe = function() {
                function t(t, e) {
                    var n = this;
                    this._element = t, this._scrollElement = "BODY" === t.tagName ? window : t, this._config = this._getConfig(e), this._selector = this._config.target + " " + ce + "," + this._config.target + " " + de + "," + this._config.target + " " + ue, this._offsets = [], this._targets = [], this._activeTarget = null, this._scrollHeight = 0, i(this._scrollElement).on(re.SCROLL, function(t) {
                        return n._process(t)
                    }), this.refresh(), this._process()
                }
                var e = t.prototype;
                return e.refresh = function() {
                    var t = this,
                        e = this._scrollElement === this._scrollElement.window ? "offset" : fe,
                        n = "auto" === this._config.method ? e : this._config.method,
                        o = n === fe ? this._getScrollTop() : 0;
                    this._offsets = [], this._targets = [], this._scrollHeight = this._getScrollHeight(), [].slice.call(document.querySelectorAll(this._selector)).map(function(t) {
                        var e, s = l.getSelectorFromElement(t);
                        if (s && (e = document.querySelector(s)), e) {
                            var r = e.getBoundingClientRect();
                            if (r.width || r.height) return [i(e)[n]().top + o, s]
                        }
                        return null
                    }).filter(function(t) {
                        return t
                    }).sort(function(t, e) {
                        return t[0] - e[0]
                    }).forEach(function(e) {
                        t._offsets.push(e[0]), t._targets.push(e[1])
                    })
                }, e.dispose = function() {
                    i.removeData(this._element, ee), i(this._scrollElement).off(ie), this._element = null, this._scrollElement = null, this._config = null, this._selector = null, this._offsets = null, this._targets = null, this._activeTarget = null, this._scrollHeight = null
                }, e._getConfig = function(t) {
                    if ("string" != typeof(t = s({}, oe, "object" == typeof t && t ? t : {})).target) {
                        var e = i(t.target).attr("id");
                        e || (e = l.getUID(te), i(t.target).attr("id", e)), t.target = "#" + e
                    }
                    return l.typeCheckConfig(te, t, se), t
                }, e._getScrollTop = function() {
                    return this._scrollElement === window ? this._scrollElement.pageYOffset : this._scrollElement.scrollTop
                }, e._getScrollHeight = function() {
                    return this._scrollElement.scrollHeight || Math.max(document.body.scrollHeight, document.documentElement.scrollHeight)
                }, e._getOffsetHeight = function() {
                    return this._scrollElement === window ? window.innerHeight : this._scrollElement.getBoundingClientRect().height
                }, e._process = function() {
                    var t = this._getScrollTop() + this._config.offset,
                        e = this._getScrollHeight(),
                        i = this._config.offset + e - this._getOffsetHeight();
                    if (this._scrollHeight !== e && this.refresh(), i <= t) {
                        var n = this._targets[this._targets.length - 1];
                        this._activeTarget !== n && this._activate(n)
                    } else {
                        if (this._activeTarget && t < this._offsets[0] && 0 < this._offsets[0]) return this._activeTarget = null, void this._clear();
                        for (var o = this._offsets.length; o--;) this._activeTarget !== this._targets[o] && t >= this._offsets[o] && (void 0 === this._offsets[o + 1] || t < this._offsets[o + 1]) && this._activate(this._targets[o])
                    }
                }, e._activate = function(t) {
                    this._activeTarget = t, this._clear();
                    var e = this._selector.split(",").map(function(e) {
                            return e + '[data-target="' + t + '"],' + e + '[href="' + t + '"]'
                        }),
                        n = i([].slice.call(document.querySelectorAll(e.join(","))));
                    n.hasClass("dropdown-item") ? (n.closest(".dropdown").find(".dropdown-toggle").addClass(ae), n.addClass(ae)) : (n.addClass(ae), n.parents(le).prev(ce + ", " + de).addClass(ae), n.parents(le).prev(".nav-item").children(ce).addClass(ae)), i(this._scrollElement).trigger(re.ACTIVATE, {
                        relatedTarget: t
                    })
                }, e._clear = function() {
                    [].slice.call(document.querySelectorAll(this._selector)).filter(function(t) {
                        return t.classList.contains(ae)
                    }).forEach(function(t) {
                        return t.classList.remove(ae)
                    })
                }, t._jQueryInterface = function(e) {
                    return this.each(function() {
                        var n = i(this).data(ee);
                        if (n || (n = new t(this, "object" == typeof e && e), i(this).data(ee, n)), "string" == typeof e) {
                            if (void 0 === n[e]) throw new TypeError('No method named "' + e + '"');
                            n[e]()
                        }
                    })
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }, {
                    key: "Default",
                    get: function() {
                        return oe
                    }
                }]), t
            }();
        i(window).on(re.LOAD_DATA_API, function() {
            for (var t = [].slice.call(document.querySelectorAll('[data-spy="scroll"]')), e = t.length; e--;) {
                var n = i(t[e]);
                pe._jQueryInterface.call(n, n.data())
            }
        }), i.fn[te] = pe._jQueryInterface, i.fn[te].Constructor = pe, i.fn[te].noConflict = function() {
            return i.fn[te] = ne, pe._jQueryInterface
        };
        var he = "bs.tab",
            me = "." + he,
            ge = i.fn.tab,
            ve = {
                HIDE: "hide" + me,
                HIDDEN: "hidden" + me,
                SHOW: "show" + me,
                SHOWN: "shown" + me,
                CLICK_DATA_API: "click" + me + ".data-api"
            },
            ye = "active",
            we = ".active",
            _e = "> li > .active",
            be = function() {
                function t(t) {
                    this._element = t
                }
                var e = t.prototype;
                return e.show = function() {
                    var t = this;
                    if (!(this._element.parentNode && this._element.parentNode.nodeType === Node.ELEMENT_NODE && i(this._element).hasClass(ye) || i(this._element).hasClass("disabled"))) {
                        var e, n, o = i(this._element).closest(".nav, .list-group")[0],
                            s = l.getSelectorFromElement(this._element);
                        if (o) {
                            var r = "UL" === o.nodeName || "OL" === o.nodeName ? _e : we;
                            n = (n = i.makeArray(i(o).find(r)))[n.length - 1]
                        }
                        var a = i.Event(ve.HIDE, {
                                relatedTarget: this._element
                            }),
                            c = i.Event(ve.SHOW, {
                                relatedTarget: n
                            });
                        if (n && i(n).trigger(a), i(this._element).trigger(c), !c.isDefaultPrevented() && !a.isDefaultPrevented()) {
                            s && (e = document.querySelector(s)), this._activate(this._element, o);
                            var d = function() {
                                var e = i.Event(ve.HIDDEN, {
                                        relatedTarget: t._element
                                    }),
                                    o = i.Event(ve.SHOWN, {
                                        relatedTarget: n
                                    });
                                i(n).trigger(e), i(t._element).trigger(o)
                            };
                            e ? this._activate(e, e.parentNode, d) : d()
                        }
                    }
                }, e.dispose = function() {
                    i.removeData(this._element, he), this._element = null
                }, e._activate = function(t, e, n) {
                    var o = this,
                        s = (!e || "UL" !== e.nodeName && "OL" !== e.nodeName ? i(e).children(we) : i(e).find(_e))[0],
                        r = n && s && i(s).hasClass("fade"),
                        a = function() {
                            return o._transitionComplete(t, s, n)
                        };
                    if (s && r) {
                        var c = l.getTransitionDurationFromElement(s);
                        i(s).removeClass("show").one(l.TRANSITION_END, a).emulateTransitionEnd(c)
                    } else a()
                }, e._transitionComplete = function(t, e, n) {
                    if (e) {
                        i(e).removeClass(ye);
                        var o = i(e.parentNode).find("> .dropdown-menu .active")[0];
                        o && i(o).removeClass(ye), "tab" === e.getAttribute("role") && e.setAttribute("aria-selected", !1)
                    }
                    if (i(t).addClass(ye), "tab" === t.getAttribute("role") && t.setAttribute("aria-selected", !0), l.reflow(t), i(t).addClass("show"), t.parentNode && i(t.parentNode).hasClass("dropdown-menu")) {
                        var s = i(t).closest(".dropdown")[0];
                        if (s) {
                            var r = [].slice.call(s.querySelectorAll(".dropdown-toggle"));
                            i(r).addClass(ye)
                        }
                        t.setAttribute("aria-expanded", !0)
                    }
                    n && n()
                }, t._jQueryInterface = function(e) {
                    return this.each(function() {
                        var n = i(this),
                            o = n.data(he);
                        if (o || (o = new t(this), n.data(he, o)), "string" == typeof e) {
                            if (void 0 === o[e]) throw new TypeError('No method named "' + e + '"');
                            o[e]()
                        }
                    })
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }]), t
            }();
        i(document).on(ve.CLICK_DATA_API, '[data-toggle="tab"], [data-toggle="pill"], [data-toggle="list"]', function(t) {
            t.preventDefault(), be._jQueryInterface.call(i(this), "show")
        }), i.fn.tab = be._jQueryInterface, i.fn.tab.Constructor = be, i.fn.tab.noConflict = function() {
            return i.fn.tab = ge, be._jQueryInterface
        };
        var Se = "toast",
            xe = "bs.toast",
            Te = "." + xe,
            ke = i.fn[Se],
            Ce = {
                CLICK_DISMISS: "click.dismiss" + Te,
                HIDE: "hide" + Te,
                HIDDEN: "hidden" + Te,
                SHOW: "show" + Te,
                SHOWN: "shown" + Te
            },
            Ee = "show",
            Ae = "showing",
            Oe = {
                animation: "boolean",
                autohide: "boolean",
                delay: "number"
            },
            Ie = {
                animation: !0,
                autohide: !0,
                delay: 500
            },
            $e = function() {
                function t(t, e) {
                    this._element = t, this._config = this._getConfig(e), this._timeout = null, this._setListeners()
                }
                var e = t.prototype;
                return e.show = function() {
                    var t = this;
                    i(this._element).trigger(Ce.SHOW), this._config.animation && this._element.classList.add("fade");
                    var e = function() {
                        t._element.classList.remove(Ae), t._element.classList.add(Ee), i(t._element).trigger(Ce.SHOWN), t._config.autohide && t.hide()
                    };
                    if (this._element.classList.remove("hide"), this._element.classList.add(Ae), this._config.animation) {
                        var n = l.getTransitionDurationFromElement(this._element);
                        i(this._element).one(l.TRANSITION_END, e).emulateTransitionEnd(n)
                    } else e()
                }, e.hide = function(t) {
                    var e = this;
                    this._element.classList.contains(Ee) && (i(this._element).trigger(Ce.HIDE), t ? this._close() : this._timeout = setTimeout(function() {
                        e._close()
                    }, this._config.delay))
                }, e.dispose = function() {
                    clearTimeout(this._timeout), this._timeout = null, this._element.classList.contains(Ee) && this._element.classList.remove(Ee), i(this._element).off(Ce.CLICK_DISMISS), i.removeData(this._element, xe), this._element = null, this._config = null
                }, e._getConfig = function(t) {
                    return t = s({}, Ie, i(this._element).data(), "object" == typeof t && t ? t : {}), l.typeCheckConfig(Se, t, this.constructor.DefaultType), t
                }, e._setListeners = function() {
                    var t = this;
                    i(this._element).on(Ce.CLICK_DISMISS, '[data-dismiss="toast"]', function() {
                        return t.hide(!0)
                    })
                }, e._close = function() {
                    var t = this,
                        e = function() {
                            t._element.classList.add("hide"), i(t._element).trigger(Ce.HIDDEN)
                        };
                    if (this._element.classList.remove(Ee), this._config.animation) {
                        var n = l.getTransitionDurationFromElement(this._element);
                        i(this._element).one(l.TRANSITION_END, e).emulateTransitionEnd(n)
                    } else e()
                }, t._jQueryInterface = function(e) {
                    return this.each(function() {
                        var n = i(this),
                            o = n.data(xe);
                        if (o || (o = new t(this, "object" == typeof e && e), n.data(xe, o)), "string" == typeof e) {
                            if (void 0 === o[e]) throw new TypeError('No method named "' + e + '"');
                            o[e](this)
                        }
                    })
                }, o(t, null, [{
                    key: "VERSION",
                    get: function() {
                        return "4.2.1"
                    }
                }, {
                    key: "DefaultType",
                    get: function() {
                        return Oe
                    }
                }]), t
            }();
        i.fn[Se] = $e._jQueryInterface, i.fn[Se].Constructor = $e, i.fn[Se].noConflict = function() {
                return i.fn[Se] = ke, $e._jQueryInterface
            },
            function() {
                if (void 0 === i) throw new TypeError("Bootstrap's JavaScript requires jQuery. jQuery must be included before Bootstrap's JavaScript.");
                var t = i.fn.jquery.split(" ")[0].split(".");
                if (t[0] < 2 && t[1] < 9 || 1 === t[0] && 9 === t[1] && t[2] < 1 || 4 <= t[0]) throw new Error("Bootstrap's JavaScript requires at least jQuery v1.9.1 but less than v4.0.0")
            }(), t.Util = l, t.Alert = h, t.Button = T, t.Carousel = M, t.Collapse = Z, t.Dropdown = pt, t.Modal = Et, t.Popover = Jt, t.Scrollspy = pe, t.Tab = be, t.Toast = $e, t.Tooltip = qt, Object.defineProperty(t, "__esModule", {
                value: !0
            })
    })
}, function(t, e, i) {
    "use strict";
    var n, o;
    ! function(s, r) {
        n = [i(0)], void 0 !== (o = function(t) {
            r(t)
        }.apply(e, n)) && (t.exports = o)
    }(0, function(t) {
        function e(e, i) {
            this.$target = t(e), this.opts = t.extend({}, l, i, this.$target.data()), void 0 === this.isOpen && this._init()
        }
        var i, n, o, s, r, a, l = {
            loadingNotice: "Loading image",
            errorNotice: "The image could not be loaded",
            errorDuration: 2500,
            linkAttribute: "href",
            preventClicks: !0,
            beforeShow: t.noop,
            beforeHide: t.noop,
            onShow: t.noop,
            onHide: t.noop,
            onMove: t.noop
        };
        return e.prototype._init = function() {
            this.$link = this.$target.find("a"), this.$image = this.$target.find("img"), this.$flyout = t('<div class="easyzoom-flyout" />'), this.$notice = t('<div class="easyzoom-notice" />'), this.$target.on({
                "mousemove.easyzoom touchmove.easyzoom": t.proxy(this._onMove, this),
                "mouseleave.easyzoom touchend.easyzoom": t.proxy(this._onLeave, this),
                "mouseenter.easyzoom touchstart.easyzoom": t.proxy(this._onEnter, this)
            }), this.opts.preventClicks && this.$target.on("click.easyzoom", function(t) {
                t.preventDefault()
            })
        }, e.prototype.show = function(t, e) {
            var r = this;
            if (!1 !== this.opts.beforeShow.call(this)) {
                if (!this.isReady) return this._loadImage(this.$link.attr(this.opts.linkAttribute), function() {
                    !r.isMouseOver && e || r.show(t)
                });
                this.$target.append(this.$flyout);
                var a = this.$target.outerWidth(),
                    l = this.$target.outerHeight(),
                    c = this.$flyout.width(),
                    d = this.$flyout.height(),
                    u = this.$zoom.width(),
                    f = this.$zoom.height();
                (i = u - c) < 0 && (i = 0), (n = f - d) < 0 && (n = 0), o = i / a, s = n / l, this.isOpen = !0, this.opts.onShow.call(this), t && this._move(t)
            }
        }, e.prototype._onEnter = function(t) {
            var e = t.originalEvent.touches;
            this.isMouseOver = !0, e && 1 != e.length || (t.preventDefault(), this.show(t, !0))
        }, e.prototype._onMove = function(t) {
            this.isOpen && (t.preventDefault(), this._move(t))
        }, e.prototype._onLeave = function() {
            this.isMouseOver = !1, this.isOpen && this.hide()
        }, e.prototype._onLoad = function(t) {
            t.currentTarget.width && (this.isReady = !0, this.$notice.detach(), this.$flyout.html(this.$zoom), this.$target.removeClass("is-loading").addClass("is-ready"), t.data.call && t.data())
        }, e.prototype._onError = function() {
            var t = this;
            this.$notice.text(this.opts.errorNotice), this.$target.removeClass("is-loading").addClass("is-error"), this.detachNotice = setTimeout(function() {
                t.$notice.detach(), t.detachNotice = null
            }, this.opts.errorDuration)
        }, e.prototype._loadImage = function(e, i) {
            var n = new Image;
            this.$target.addClass("is-loading").append(this.$notice.text(this.opts.loadingNotice)), this.$zoom = t(n).on("error", t.proxy(this._onError, this)).on("load", i, t.proxy(this._onLoad, this)), n.style.position = "absolute", n.src = e
        }, e.prototype._move = function(t) {
            if (0 === t.type.indexOf("touch")) {
                var e = t.touches || t.originalEvent.touches;
                r = e[0].pageX, a = e[0].pageY
            } else r = t.pageX || r, a = t.pageY || a;
            var l = this.$target.offset(),
                c = a - l.top,
                d = r - l.left,
                u = Math.ceil(c * s),
                f = Math.ceil(d * o);
            if (f < 0 || u < 0 || i < f || n < u) this.hide();
            else {
                var p = -1 * u,
                    h = -1 * f;
                this.$zoom.css({
                    top: p,
                    left: h
                }), this.opts.onMove.call(this, p, h)
            }
        }, e.prototype.hide = function() {
            this.isOpen && !1 !== this.opts.beforeHide.call(this) && (this.$flyout.detach(), this.isOpen = !1, this.opts.onHide.call(this))
        }, e.prototype.swap = function(e, i, n) {
            this.hide(), this.isReady = !1, this.detachNotice && clearTimeout(this.detachNotice), this.$notice.parent().length && this.$notice.detach(), this.$target.removeClass("is-loading is-ready is-error"), this.$image.attr({
                src: e,
                srcset: t.isArray(n) ? n.join() : n
            }), this.$link.attr(this.opts.linkAttribute, i)
        }, e.prototype.teardown = function() {
            this.hide(), this.$target.off(".easyzoom").removeClass("is-loading is-ready is-error"), this.detachNotice && clearTimeout(this.detachNotice), delete this.$link, delete this.$zoom, delete this.$image, delete this.$notice, delete this.$flyout, delete this.isOpen, delete this.isReady
        }, t.fn.easyZoom = function(i) {
            return this.each(function() {
                var n = t.data(this, "easyZoom");
                n ? void 0 === n.isOpen && n._init() : t.data(this, "easyZoom", new e(this, i))
            })
        }, e
    })
}, function(t, e, i) {
    "use strict";

    function n() {
        this._events = this._events || {}, this._maxListeners = this._maxListeners || void 0
    }

    function o(t) {
        return "function" == typeof t
    }

    function s(t) {
        return "number" == typeof t
    }

    function r(t) {
        return "object" == typeof t && null !== t
    }

    function a(t) {
        return void 0 === t
    }
    t.exports = n, n.EventEmitter = n, n.prototype._events = void 0, n.prototype._maxListeners = void 0, n.defaultMaxListeners = 10, n.prototype.setMaxListeners = function(t) {
        if (!s(t) || t < 0 || isNaN(t)) throw TypeError("n must be a positive number");
        return this._maxListeners = t, this
    }, n.prototype.emit = function(t) {
        var e, i, n, s, l, c;
        if (this._events || (this._events = {}), "error" === t && (!this._events.error || r(this._events.error) && !this._events.error.length)) {
            if ((e = arguments[1]) instanceof Error) throw e;
            var d = new Error('Uncaught, unspecified "error" event. (' + e + ")");
            throw d.context = e, d
        }
        if (i = this._events[t], a(i)) return !1;
        if (o(i)) switch (arguments.length) {
            case 1:
                i.call(this);
                break;
            case 2:
                i.call(this, arguments[1]);
                break;
            case 3:
                i.call(this, arguments[1], arguments[2]);
                break;
            default:
                s = Array.prototype.slice.call(arguments, 1), i.apply(this, s)
        } else if (r(i))
            for (s = Array.prototype.slice.call(arguments, 1), c = i.slice(), n = c.length, l = 0; l < n; l++) c[l].apply(this, s);
        return !0
    }, n.prototype.addListener = function(t, e) {
        var i;
        if (!o(e)) throw TypeError("listener must be a function");
        return this._events || (this._events = {}), this._events.newListener && this.emit("newListener", t, o(e.listener) ? e.listener : e), this._events[t] ? r(this._events[t]) ? this._events[t].push(e) : this._events[t] = [this._events[t], e] : this._events[t] = e, r(this._events[t]) && !this._events[t].warned && (i = a(this._maxListeners) ? n.defaultMaxListeners : this._maxListeners) && i > 0 && this._events[t].length > i && (this._events[t].warned = !0, console.trace), this
    }, n.prototype.on = n.prototype.addListener, n.prototype.once = function(t, e) {
        function i() {
            this.removeListener(t, i), n || (n = !0, e.apply(this, arguments))
        }
        if (!o(e)) throw TypeError("listener must be a function");
        var n = !1;
        return i.listener = e, this.on(t, i), this
    }, n.prototype.removeListener = function(t, e) {
        var i, n, s, a;
        if (!o(e)) throw TypeError("listener must be a function");
        if (!this._events || !this._events[t]) return this;
        if (i = this._events[t], s = i.length, n = -1, i === e || o(i.listener) && i.listener === e) delete this._events[t], this._events.removeListener && this.emit("removeListener", t, e);
        else if (r(i)) {
            for (a = s; a-- > 0;)
                if (i[a] === e || i[a].listener && i[a].listener === e) {
                    n = a;
                    break
                } if (n < 0) return this;
            1 === i.length ? (i.length = 0, delete this._events[t]) : i.splice(n, 1), this._events.removeListener && this.emit("removeListener", t, e)
        }
        return this
    }, n.prototype.removeAllListeners = function(t) {
        var e, i;
        if (!this._events) return this;
        if (!this._events.removeListener) return 0 === arguments.length ? this._events = {} : this._events[t] && delete this._events[t], this;
        if (0 === arguments.length) {
            for (e in this._events) "removeListener" !== e && this.removeAllListeners(e);
            return this.removeAllListeners("removeListener"), this._events = {}, this
        }
        if (i = this._events[t], o(i)) this.removeListener(t, i);
        else if (i)
            for (; i.length;) this.removeListener(t, i[i.length - 1]);
        return delete this._events[t], this
    }, n.prototype.listeners = function(t) {
        return this._events && this._events[t] ? o(this._events[t]) ? [this._events[t]] : this._events[t].slice() : []
    }, n.prototype.listenerCount = function(t) {
        if (this._events) {
            var e = this._events[t];
            if (o(e)) return 1;
            if (e) return e.length
        }
        return 0
    }, n.listenerCount = function(t, e) {
        return t.listenerCount(e)
    }
}, function(t, e, i) {
    "use strict";
    var n, n;
    ! function(e) {
        t.exports = e()
    }(function() {
        return function t(e, i, o) {
            function s(a, l) {
                if (!i[a]) {
                    if (!e[a]) {
                        var c = "function" == typeof n && n;
                        if (!l && c) return n(a, !0);
                        if (r) return r(a, !0);
                        var d = new Error("Cannot find module '" + a + "'");
                        throw d.code = "MODULE_NOT_FOUND", d
                    }
                    var u = i[a] = {
                        exports: {}
                    };
                    e[a][0].call(u.exports, function(t) {
                        var i = e[a][1][t];
                        return s(i || t)
                    }, u, u.exports, t, e, i, o)
                }
                return i[a].exports
            }
            for (var r = "function" == typeof n && n, a = 0; a < o.length; a++) s(o[a]);
            return s
        }({
            1: [function(t, e, i) {
                e.exports = function(t) {
                    var e, i, n, o = -1;
                    if (t.lines.length > 1 && "flex-start" === t.style.alignContent)
                        for (e = 0; n = t.lines[++o];) n.crossStart = e, e += n.cross;
                    else if (t.lines.length > 1 && "flex-end" === t.style.alignContent)
                        for (e = t.flexStyle.crossSpace; n = t.lines[++o];) n.crossStart = e, e += n.cross;
                    else if (t.lines.length > 1 && "center" === t.style.alignContent)
                        for (e = t.flexStyle.crossSpace / 2; n = t.lines[++o];) n.crossStart = e, e += n.cross;
                    else if (t.lines.length > 1 && "space-between" === t.style.alignContent)
                        for (i = t.flexStyle.crossSpace / (t.lines.length - 1), e = 0; n = t.lines[++o];) n.crossStart = e, e += n.cross + i;
                    else if (t.lines.length > 1 && "space-around" === t.style.alignContent)
                        for (i = 2 * t.flexStyle.crossSpace / (2 * t.lines.length), e = i / 2; n = t.lines[++o];) n.crossStart = e, e += n.cross + i;
                    else
                        for (i = t.flexStyle.crossSpace / t.lines.length, e = t.flexStyle.crossInnerBefore; n = t.lines[++o];) n.crossStart = e, n.cross += i, e += n.cross
                }
            }, {}],
            2: [function(t, e, i) {
                e.exports = function(t) {
                    for (var e, i = -1; line = t.lines[++i];)
                        for (e = -1; child = line.children[++e];) {
                            var n = child.style.alignSelf;
                            "auto" === n && (n = t.style.alignItems), "flex-start" === n ? child.flexStyle.crossStart = line.crossStart : "flex-end" === n ? child.flexStyle.crossStart = line.crossStart + line.cross - child.flexStyle.crossOuter : "center" === n ? child.flexStyle.crossStart = line.crossStart + (line.cross - child.flexStyle.crossOuter) / 2 : (child.flexStyle.crossStart = line.crossStart, child.flexStyle.crossOuter = line.cross, child.flexStyle.cross = child.flexStyle.crossOuter - child.flexStyle.crossBefore - child.flexStyle.crossAfter)
                        }
                }
            }, {}],
            3: [function(t, e, i) {
                e.exports = function(t, e) {
                    var i = "row" === e || "row-reverse" === e,
                        n = t.mainAxis;
                    if (n) {
                        i && "inline" === n || !i && "block" === n || (t.flexStyle = {
                            main: t.flexStyle.cross,
                            cross: t.flexStyle.main,
                            mainOffset: t.flexStyle.crossOffset,
                            crossOffset: t.flexStyle.mainOffset,
                            mainBefore: t.flexStyle.crossBefore,
                            mainAfter: t.flexStyle.crossAfter,
                            crossBefore: t.flexStyle.mainBefore,
                            crossAfter: t.flexStyle.mainAfter,
                            mainInnerBefore: t.flexStyle.crossInnerBefore,
                            mainInnerAfter: t.flexStyle.crossInnerAfter,
                            crossInnerBefore: t.flexStyle.mainInnerBefore,
                            crossInnerAfter: t.flexStyle.mainInnerAfter,
                            mainBorderBefore: t.flexStyle.crossBorderBefore,
                            mainBorderAfter: t.flexStyle.crossBorderAfter,
                            crossBorderBefore: t.flexStyle.mainBorderBefore,
                            crossBorderAfter: t.flexStyle.mainBorderAfter
                        })
                    } else t.flexStyle = i ? {
                        main: t.style.width,
                        cross: t.style.height,
                        mainOffset: t.style.offsetWidth,
                        crossOffset: t.style.offsetHeight,
                        mainBefore: t.style.marginLeft,
                        mainAfter: t.style.marginRight,
                        crossBefore: t.style.marginTop,
                        crossAfter: t.style.marginBottom,
                        mainInnerBefore: t.style.paddingLeft,
                        mainInnerAfter: t.style.paddingRight,
                        crossInnerBefore: t.style.paddingTop,
                        crossInnerAfter: t.style.paddingBottom,
                        mainBorderBefore: t.style.borderLeftWidth,
                        mainBorderAfter: t.style.borderRightWidth,
                        crossBorderBefore: t.style.borderTopWidth,
                        crossBorderAfter: t.style.borderBottomWidth
                    } : {
                        main: t.style.height,
                        cross: t.style.width,
                        mainOffset: t.style.offsetHeight,
                        crossOffset: t.style.offsetWidth,
                        mainBefore: t.style.marginTop,
                        mainAfter: t.style.marginBottom,
                        crossBefore: t.style.marginLeft,
                        crossAfter: t.style.marginRight,
                        mainInnerBefore: t.style.paddingTop,
                        mainInnerAfter: t.style.paddingBottom,
                        crossInnerBefore: t.style.paddingLeft,
                        crossInnerAfter: t.style.paddingRight,
                        mainBorderBefore: t.style.borderTopWidth,
                        mainBorderAfter: t.style.borderBottomWidth,
                        crossBorderBefore: t.style.borderLeftWidth,
                        crossBorderAfter: t.style.borderRightWidth
                    }, "content-box" === t.style.boxSizing && ("number" == typeof t.flexStyle.main && (t.flexStyle.main += t.flexStyle.mainInnerBefore + t.flexStyle.mainInnerAfter + t.flexStyle.mainBorderBefore + t.flexStyle.mainBorderAfter), "number" == typeof t.flexStyle.cross && (t.flexStyle.cross += t.flexStyle.crossInnerBefore + t.flexStyle.crossInnerAfter + t.flexStyle.crossBorderBefore + t.flexStyle.crossBorderAfter));
                    t.mainAxis = i ? "inline" : "block", t.crossAxis = i ? "block" : "inline", "number" == typeof t.style.flexBasis && (t.flexStyle.main = t.style.flexBasis + t.flexStyle.mainInnerBefore + t.flexStyle.mainInnerAfter + t.flexStyle.mainBorderBefore + t.flexStyle.mainBorderAfter), t.flexStyle.mainOuter = t.flexStyle.main, t.flexStyle.crossOuter = t.flexStyle.cross, "auto" === t.flexStyle.mainOuter && (t.flexStyle.mainOuter = t.flexStyle.mainOffset), "auto" === t.flexStyle.crossOuter && (t.flexStyle.crossOuter = t.flexStyle.crossOffset), "number" == typeof t.flexStyle.mainBefore && (t.flexStyle.mainOuter += t.flexStyle.mainBefore), "number" == typeof t.flexStyle.mainAfter && (t.flexStyle.mainOuter += t.flexStyle.mainAfter), "number" == typeof t.flexStyle.crossBefore && (t.flexStyle.crossOuter += t.flexStyle.crossBefore), "number" == typeof t.flexStyle.crossAfter && (t.flexStyle.crossOuter += t.flexStyle.crossAfter)
                }
            }, {}],
            4: [function(t, e, i) {
                var n = t("../reduce");
                e.exports = function(t) {
                    if (t.mainSpace > 0) {
                        var e = n(t.children, function(t, e) {
                            return t + parseFloat(e.style.flexGrow)
                        }, 0);
                        e > 0 && (t.main = n(t.children, function(i, n) {
                            return "auto" === n.flexStyle.main ? n.flexStyle.main = n.flexStyle.mainOffset + parseFloat(n.style.flexGrow) / e * t.mainSpace : n.flexStyle.main += parseFloat(n.style.flexGrow) / e * t.mainSpace, n.flexStyle.mainOuter = n.flexStyle.main + n.flexStyle.mainBefore + n.flexStyle.mainAfter, i + n.flexStyle.mainOuter
                        }, 0), t.mainSpace = 0)
                    }
                }
            }, {
                "../reduce": 12
            }],
            5: [function(t, e, i) {
                var n = t("../reduce");
                e.exports = function(t) {
                    if (t.mainSpace < 0) {
                        var e = n(t.children, function(t, e) {
                            return t + parseFloat(e.style.flexShrink)
                        }, 0);
                        e > 0 && (t.main = n(t.children, function(i, n) {
                            return n.flexStyle.main += parseFloat(n.style.flexShrink) / e * t.mainSpace, n.flexStyle.mainOuter = n.flexStyle.main + n.flexStyle.mainBefore + n.flexStyle.mainAfter, i + n.flexStyle.mainOuter
                        }, 0), t.mainSpace = 0)
                    }
                }
            }, {
                "../reduce": 12
            }],
            6: [function(t, e, i) {
                var n = t("../reduce");
                e.exports = function(t) {
                    var e;
                    t.lines = [e = {
                        main: 0,
                        cross: 0,
                        children: []
                    }];
                    for (var i, o = -1; i = t.children[++o];) "nowrap" === t.style.flexWrap || 0 === e.children.length || "auto" === t.flexStyle.main || t.flexStyle.main - t.flexStyle.mainInnerBefore - t.flexStyle.mainInnerAfter - t.flexStyle.mainBorderBefore - t.flexStyle.mainBorderAfter >= e.main + i.flexStyle.mainOuter ? (e.main += i.flexStyle.mainOuter, e.cross = Math.max(e.cross, i.flexStyle.crossOuter)) : t.lines.push(e = {
                        main: i.flexStyle.mainOuter,
                        cross: i.flexStyle.crossOuter,
                        children: []
                    }), e.children.push(i);
                    t.flexStyle.mainLines = n(t.lines, function(t, e) {
                        return Math.max(t, e.main)
                    }, 0), t.flexStyle.crossLines = n(t.lines, function(t, e) {
                        return t + e.cross
                    }, 0), "auto" === t.flexStyle.main && (t.flexStyle.main = Math.max(t.flexStyle.mainOffset, t.flexStyle.mainLines + t.flexStyle.mainInnerBefore + t.flexStyle.mainInnerAfter + t.flexStyle.mainBorderBefore + t.flexStyle.mainBorderAfter)), "auto" === t.flexStyle.cross && (t.flexStyle.cross = Math.max(t.flexStyle.crossOffset, t.flexStyle.crossLines + t.flexStyle.crossInnerBefore + t.flexStyle.crossInnerAfter + t.flexStyle.crossBorderBefore + t.flexStyle.crossBorderAfter)), t.flexStyle.crossSpace = t.flexStyle.cross - t.flexStyle.crossInnerBefore - t.flexStyle.crossInnerAfter - t.flexStyle.crossBorderBefore - t.flexStyle.crossBorderAfter - t.flexStyle.crossLines, t.flexStyle.mainOuter = t.flexStyle.main + t.flexStyle.mainBefore + t.flexStyle.mainAfter, t.flexStyle.crossOuter = t.flexStyle.cross + t.flexStyle.crossBefore + t.flexStyle.crossAfter
                }
            }, {
                "../reduce": 12
            }],
            7: [function(t, e, i) {
                function n(e) {
                    for (var i, n = -1; i = e.children[++n];) t("./flex-direction")(i, e.style.flexDirection);
                    t("./flex-direction")(e, e.style.flexDirection), t("./order")(e), t("./flexbox-lines")(e), t("./align-content")(e), n = -1;
                    for (var o; o = e.lines[++n];) o.mainSpace = e.flexStyle.main - e.flexStyle.mainInnerBefore - e.flexStyle.mainInnerAfter - e.flexStyle.mainBorderBefore - e.flexStyle.mainBorderAfter - o.main, t("./flex-grow")(o), t("./flex-shrink")(o), t("./margin-main")(o), t("./margin-cross")(o), t("./justify-content")(o, e.style.justifyContent, e);
                    t("./align-items")(e)
                }
                e.exports = n
            }, {
                "./align-content": 1,
                "./align-items": 2,
                "./flex-direction": 3,
                "./flex-grow": 4,
                "./flex-shrink": 5,
                "./flexbox-lines": 6,
                "./justify-content": 8,
                "./margin-cross": 9,
                "./margin-main": 10,
                "./order": 11
            }],
            8: [function(t, e, i) {
                e.exports = function(t, e, i) {
                    var n, o, s, r = i.flexStyle.mainInnerBefore,
                        a = -1;
                    if ("flex-end" === e)
                        for (n = t.mainSpace, n += r; s = t.children[++a];) s.flexStyle.mainStart = n, n += s.flexStyle.mainOuter;
                    else if ("center" === e)
                        for (n = t.mainSpace / 2, n += r; s = t.children[++a];) s.flexStyle.mainStart = n, n += s.flexStyle.mainOuter;
                    else if ("space-between" === e)
                        for (o = t.mainSpace / (t.children.length - 1), n = 0, n += r; s = t.children[++a];) s.flexStyle.mainStart = n, n += s.flexStyle.mainOuter + o;
                    else if ("space-around" === e)
                        for (o = 2 * t.mainSpace / (2 * t.children.length), n = o / 2, n += r; s = t.children[++a];) s.flexStyle.mainStart = n, n += s.flexStyle.mainOuter + o;
                    else
                        for (n = 0, n += r; s = t.children[++a];) s.flexStyle.mainStart = n, n += s.flexStyle.mainOuter
                }
            }, {}],
            9: [function(t, e, i) {
                e.exports = function(t) {
                    for (var e, i = -1; e = t.children[++i];) {
                        var n = 0;
                        "auto" === e.flexStyle.crossBefore && ++n, "auto" === e.flexStyle.crossAfter && ++n;
                        var o = t.cross - e.flexStyle.crossOuter;
                        "auto" === e.flexStyle.crossBefore && (e.flexStyle.crossBefore = o / n), "auto" === e.flexStyle.crossAfter && (e.flexStyle.crossAfter = o / n), "auto" === e.flexStyle.cross ? e.flexStyle.crossOuter = e.flexStyle.crossOffset + e.flexStyle.crossBefore + e.flexStyle.crossAfter : e.flexStyle.crossOuter = e.flexStyle.cross + e.flexStyle.crossBefore + e.flexStyle.crossAfter
                    }
                }
            }, {}],
            10: [function(t, e, i) {
                e.exports = function(t) {
                    for (var e, i = 0, n = -1; e = t.children[++n];) "auto" === e.flexStyle.mainBefore && ++i, "auto" === e.flexStyle.mainAfter && ++i;
                    if (i > 0) {
                        for (n = -1; e = t.children[++n];) "auto" === e.flexStyle.mainBefore && (e.flexStyle.mainBefore = t.mainSpace / i), "auto" === e.flexStyle.mainAfter && (e.flexStyle.mainAfter = t.mainSpace / i), "auto" === e.flexStyle.main ? e.flexStyle.mainOuter = e.flexStyle.mainOffset + e.flexStyle.mainBefore + e.flexStyle.mainAfter : e.flexStyle.mainOuter = e.flexStyle.main + e.flexStyle.mainBefore + e.flexStyle.mainAfter;
                        t.mainSpace = 0
                    }
                }
            }, {}],
            11: [function(t, e, i) {
                var n = /^(column|row)-reverse$/;
                e.exports = function(t) {
                    t.children.sort(function(t, e) {
                        return t.style.order - e.style.order || t.index - e.index
                    }), n.test(t.style.flexDirection) && t.children.reverse()
                }
            }, {}],
            12: [function(t, e, i) {
                function n(t, e, i) {
                    for (var n = t.length, o = -1; ++o < n;) o in t && (i = e(i, t[o], o));
                    return i
                }
                e.exports = n
            }, {}],
            13: [function(t, e, i) {
                function n(t) {
                    a(r(t))
                }
                var o = t("./read"),
                    s = t("./write"),
                    r = t("./readAll"),
                    a = t("./writeAll");
                e.exports = n, e.exports.read = o, e.exports.write = s, e.exports.readAll = r, e.exports.writeAll = a
            }, {
                "./read": 15,
                "./readAll": 16,
                "./write": 17,
                "./writeAll": 18
            }],
            14: [function(t, e, i) {
                function n(t, e) {
                    var i = String(t).match(s);
                    if (!i) return t;
                    var n = i[1],
                        r = i[2];
                    return "px" === r ? 1 * n : "cm" === r ? .3937 * n * 96 : "in" === r ? 96 * n : "mm" === r ? .3937 * n * 96 / 10 : "pc" === r ? 12 * n * 96 / 72 : "pt" === r ? 96 * n / 72 : "rem" === r ? 16 * n : o(t, e)
                }

                function o(t, e) {
                    r.style.cssText = "border:none!important;clip:rect(0 0 0 0)!important;display:block!important;font-size:1em!important;height:0!important;margin:0!important;padding:0!important;position:relative!important;width:" + t + "!important", e.parentNode.insertBefore(r, e.nextSibling);
                    var i = r.offsetWidth;
                    return e.parentNode.removeChild(r), i
                }
                e.exports = n;
                var s = /^([-+]?\d*\.?\d+)(%|[a-z]+)$/,
                    r = document.createElement("div")
            }, {}],
            15: [function(t, e, i) {
                function n(t) {
                    var e = {
                        alignContent: "stretch",
                        alignItems: "stretch",
                        alignSelf: "auto",
                        borderBottomWidth: 0,
                        borderLeftWidth: 0,
                        borderRightWidth: 0,
                        borderTopWidth: 0,
                        boxSizing: "content-box",
                        display: "inline",
                        flexBasis: "auto",
                        flexDirection: "row",
                        flexGrow: 0,
                        flexShrink: 1,
                        flexWrap: "nowrap",
                        justifyContent: "flex-start",
                        height: "auto",
                        marginTop: 0,
                        marginRight: 0,
                        marginLeft: 0,
                        marginBottom: 0,
                        paddingTop: 0,
                        paddingRight: 0,
                        paddingLeft: 0,
                        paddingBottom: 0,
                        maxHeight: "none",
                        maxWidth: "none",
                        minHeight: 0,
                        minWidth: 0,
                        order: 0,
                        position: "static",
                        width: "auto"
                    };
                    if (t instanceof Element) {
                        var i = t.hasAttribute("data-style"),
                            n = i ? t.getAttribute("data-style") : t.getAttribute("style") || "";
                        i || t.setAttribute("data-style", n), r(e, window.getComputedStyle && getComputedStyle(t) || {}), o(e, t.currentStyle || {}), s(e, n);
                        for (var a in e) e[a] = l(e[a], t);
                        var c = t.getBoundingClientRect();
                        e.offsetHeight = c.height || t.offsetHeight, e.offsetWidth = c.width || t.offsetWidth
                    }
                    return {
                        element: t,
                        style: e
                    }
                }

                function o(t, e) {
                    for (var i in t) {
                        if (i in e) t[i] = e[i];
                        else {
                            var n = i.replace(/[A-Z]/g, "-$&").toLowerCase();
                            n in e && (t[i] = e[n])
                        }
                    }
                    "-js-display" in e && (t.display = e["-js-display"])
                }

                function s(t, e) {
                    for (var i; i = a.exec(e);) {
                        t[i[1].toLowerCase().replace(/-[a-z]/g, function(t) {
                            return t.slice(1).toUpperCase()
                        })] = i[2]
                    }
                }

                function r(t, e) {
                    for (var i in t) {
                        i in e && !/^(alignSelf|height|width)$/.test(i) && (t[i] = e[i])
                    }
                }
                e.exports = n;
                var a = /([^\s:;]+)\s*:\s*([^;]+?)\s*(;|$)/g,
                    l = t("./getComputedLength")
            }, {
                "./getComputedLength": 14
            }],
            16: [function(t, e, i) {
                function n(t) {
                    var e = [];
                    return o(t, e), e
                }

                function o(t, e) {
                    for (var i, n = s(t), a = [], l = -1; i = t.childNodes[++l];) {
                        var c = 3 === i.nodeType && !/^\s*$/.test(i.nodeValue);
                        if (n && c) {
                            var d = i;
                            i = t.insertBefore(document.createElement("flex-item"), d), i.appendChild(d)
                        }
                        if (i instanceof Element) {
                            var u = o(i, e);
                            if (n) {
                                var f = i.style;
                                f.display = "inline-block", f.position = "absolute", u.style = r(i).style, a.push(u)
                            }
                        }
                    }
                    var p = {
                        element: t,
                        children: a
                    };
                    return n && (p.style = r(t).style, e.push(p)), p
                }

                function s(t) {
                    var e = t instanceof Element,
                        i = e && t.getAttribute("data-style"),
                        n = e && t.currentStyle && t.currentStyle["-js-display"];
                    return a.test(i) || l.test(n)
                }
                e.exports = n;
                var r = t("../read"),
                    a = /(^|;)\s*display\s*:\s*(inline-)?flex\s*(;|$)/i,
                    l = /^(inline-)?flex$/i
            }, {
                "../read": 15
            }],
            17: [function(t, e, i) {
                function n(t) {
                    s(t);
                    var e = t.element.style,
                        i = "inline" === t.mainAxis ? ["main", "cross"] : ["cross", "main"];
                    e.boxSizing = "content-box", e.display = "block", e.position = "relative", e.width = o(t.flexStyle[i[0]] - t.flexStyle[i[0] + "InnerBefore"] - t.flexStyle[i[0] + "InnerAfter"] - t.flexStyle[i[0] + "BorderBefore"] - t.flexStyle[i[0] + "BorderAfter"]), e.height = o(t.flexStyle[i[1]] - t.flexStyle[i[1] + "InnerBefore"] - t.flexStyle[i[1] + "InnerAfter"] - t.flexStyle[i[1] + "BorderBefore"] - t.flexStyle[i[1] + "BorderAfter"]);
                    for (var n, r = -1; n = t.children[++r];) {
                        var a = n.element.style,
                            l = "inline" === n.mainAxis ? ["main", "cross"] : ["cross", "main"];
                        a.boxSizing = "content-box", a.display = "block", a.position = "absolute", "auto" !== n.flexStyle[l[0]] && (a.width = o(n.flexStyle[l[0]] - n.flexStyle[l[0] + "InnerBefore"] - n.flexStyle[l[0] + "InnerAfter"] - n.flexStyle[l[0] + "BorderBefore"] - n.flexStyle[l[0] + "BorderAfter"])), "auto" !== n.flexStyle[l[1]] && (a.height = o(n.flexStyle[l[1]] - n.flexStyle[l[1] + "InnerBefore"] - n.flexStyle[l[1] + "InnerAfter"] - n.flexStyle[l[1] + "BorderBefore"] - n.flexStyle[l[1] + "BorderAfter"])), a.top = o(n.flexStyle[l[1] + "Start"]), a.left = o(n.flexStyle[l[0] + "Start"]), a.marginTop = o(n.flexStyle[l[1] + "Before"]), a.marginRight = o(n.flexStyle[l[0] + "After"]), a.marginBottom = o(n.flexStyle[l[1] + "After"]), a.marginLeft = o(n.flexStyle[l[0] + "Before"])
                    }
                }

                function o(t) {
                    return "string" == typeof t ? t : Math.max(t, 0) + "px"
                }
                e.exports = n;
                var s = t("../flexbox")
            }, {
                "../flexbox": 7
            }],
            18: [function(t, e, i) {
                function n(t) {
                    for (var e, i = -1; e = t[++i];) o(e)
                }
                e.exports = n;
                var o = t("../write")
            }, {
                "../write": 17
            }]
        }, {}, [13])(13)
    })
}, function(t, e, i) {
    "use strict";
    (function(i) {
        function n(t) {
            var e = !1;
            return function() {
                e || (e = !0, window.Promise.resolve().then(function() {
                    e = !1, t()
                }))
            }
        }

        function o(t) {
            var e = !1;
            return function() {
                e || (e = !0, setTimeout(function() {
                    e = !1, t()
                }, pt))
            }
        }

        function s(t) {
            var e = {};
            return t && "[object Function]" === e.toString.call(t)
        }

        function r(t, e) {
            if (1 !== t.nodeType) return [];
            var i = t.ownerDocument.defaultView,
                n = i.getComputedStyle(t, null);
            return e ? n[e] : n
        }

        function a(t) {
            return "HTML" === t.nodeName ? t : t.parentNode || t.host
        }

        function l(t) {
            for (var e = !0; e;) {
                var i = t;
                if (e = !1, !i) return document.body;
                switch (i.nodeName) {
                    case "HTML":
                    case "BODY":
                        return i.ownerDocument.body;
                    case "#document":
                        return i.body
                }
                var n = r(i),
                    o = n.overflow,
                    s = n.overflowX,
                    l = n.overflowY;
                if (/(auto|scroll|overlay)/.test(o + l + s)) return i;
                t = a(i), e = !0, n = o = s = l = void 0
            }
        }

        function c(t) {
            return 11 === t ? vt : 10 === t ? yt : vt || yt
        }

        function d(t) {
            for (var e = !0; e;) {
                var i = t;
                if (e = !1, !i) return document.documentElement;
                for (var n = c(10) ? document.body : null, o = i.offsetParent || null; o === n && i.nextElementSibling;) o = (i = i.nextElementSibling).offsetParent;
                var s = o && o.nodeName;
                if (!s || "BODY" === s || "HTML" === s) return i ? i.ownerDocument.documentElement : document.documentElement;
                if (-1 === ["TH", "TD", "TABLE"].indexOf(o.nodeName) || "static" !== r(o, "position")) return o;
                t = o, e = !0, n = o = s = void 0
            }
        }

        function u(t) {
            var e = t.nodeName;
            return "BODY" !== e && ("HTML" === e || d(t.firstElementChild) === t)
        }

        function f(t) {
            for (var e = !0; e;) {
                var i = t;
                e = !1; {
                    if (null === i.parentNode) return i;
                    t = i.parentNode, e = !0
                }
            }
        }

        function p(t, e) {
            for (var i = !0; i;) {
                var n = t,
                    o = e;
                if (i = !1, !(n && n.nodeType && o && o.nodeType)) return document.documentElement;
                var s = n.compareDocumentPosition(o) & Node.DOCUMENT_POSITION_FOLLOWING,
                    r = s ? n : o,
                    a = s ? o : n,
                    l = document.createRange();
                l.setStart(r, 0), l.setEnd(a, 0);
                var c = l.commonAncestorContainer;
                if (n !== c && o !== c || r.contains(a)) return u(c) ? c : d(c);
                var p = f(n);
                p.host ? (t = p.host, e = o, i = !0, s = r = a = l = c = p = void 0) : (t = n, e = f(o).host, i = !0, s = r = a = l = c = p = void 0)
            }
        }

        function h(t) {
            var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : "top",
                i = "top" === e ? "scrollTop" : "scrollLeft",
                n = t.nodeName;
            if ("BODY" === n || "HTML" === n) {
                var o = t.ownerDocument.documentElement;
                return (t.ownerDocument.scrollingElement || o)[i]
            }
            return t[i]
        }

        function m(t, e) {
            var i = arguments.length > 2 && void 0 !== arguments[2] && arguments[2],
                n = h(e, "top"),
                o = h(e, "left"),
                s = i ? -1 : 1;
            return t.top += n * s, t.bottom += n * s, t.left += o * s, t.right += o * s, t
        }

        function g(t, e) {
            var i = "x" === e ? "Left" : "Top",
                n = "Left" === i ? "Right" : "Bottom";
            return parseFloat(t["border" + i + "Width"], 10) + parseFloat(t["border" + n + "Width"], 10)
        }

        function v(t, e, i, n) {
            return Math.max(e["offset" + t], e["scroll" + t], i["client" + t], i["offset" + t], i["scroll" + t], c(10) ? parseInt(i["offset" + t]) + parseInt(n["margin" + ("Height" === t ? "Top" : "Left")]) + parseInt(n["margin" + ("Height" === t ? "Bottom" : "Right")]) : 0)
        }

        function y(t) {
            var e = t.body,
                i = t.documentElement,
                n = c(10) && getComputedStyle(i);
            return {
                height: v("Height", e, i, n),
                width: v("Width", e, i, n)
            }
        }

        function w(t) {
            return St({}, t, {
                right: t.left + t.width,
                bottom: t.top + t.height
            })
        }

        function _(t) {
            var e = {};
            try {
                if (c(10)) {
                    e = t.getBoundingClientRect();
                    var i = h(t, "top"),
                        n = h(t, "left");
                    e.top += i, e.left += n, e.bottom += i, e.right += n
                } else e = t.getBoundingClientRect()
            } catch (t) {}
            var o = {
                    left: e.left,
                    top: e.top,
                    width: e.right - e.left,
                    height: e.bottom - e.top
                },
                s = "HTML" === t.nodeName ? y(t.ownerDocument) : {},
                a = s.width || t.clientWidth || o.right - o.left,
                l = s.height || t.clientHeight || o.bottom - o.top,
                d = t.offsetWidth - a,
                u = t.offsetHeight - l;
            if (d || u) {
                var f = r(t);
                d -= g(f, "x"), u -= g(f, "y"), o.width -= d, o.height -= u
            }
            return w(o)
        }

        function b(t, e) {
            var i = arguments.length > 2 && void 0 !== arguments[2] && arguments[2],
                n = c(10),
                o = "HTML" === e.nodeName,
                s = _(t),
                a = _(e),
                d = l(t),
                u = r(e),
                f = parseFloat(u.borderTopWidth, 10),
                p = parseFloat(u.borderLeftWidth, 10);
            i && o && (a.top = Math.max(a.top, 0), a.left = Math.max(a.left, 0));
            var h = w({
                top: s.top - a.top - f,
                left: s.left - a.left - p,
                width: s.width,
                height: s.height
            });
            if (h.marginTop = 0, h.marginLeft = 0, !n && o) {
                var g = parseFloat(u.marginTop, 10),
                    v = parseFloat(u.marginLeft, 10);
                h.top -= f - g, h.bottom -= f - g, h.left -= p - v, h.right -= p - v, h.marginTop = g, h.marginLeft = v
            }
            return (n && !i ? e.contains(d) : e === d && "BODY" !== d.nodeName) && (h = m(h, e)), h
        }

        function S(t) {
            var e = arguments.length > 1 && void 0 !== arguments[1] && arguments[1],
                i = t.ownerDocument.documentElement,
                n = b(t, i),
                o = Math.max(i.clientWidth, window.innerWidth || 0),
                s = Math.max(i.clientHeight, window.innerHeight || 0),
                r = e ? 0 : h(i),
                a = e ? 0 : h(i, "left");
            return w({
                top: r - n.top + n.marginTop,
                left: a - n.left + n.marginLeft,
                width: o,
                height: s
            })
        }

        function x(t) {
            for (var e = !0; e;) {
                var i = t;
                e = !1;
                var n = i.nodeName;
                if ("BODY" === n || "HTML" === n) return !1;
                if ("fixed" === r(i, "position")) return !0;
                t = a(i), e = !0, n = void 0
            }
        }

        function T(t) {
            if (!t || !t.parentElement || c()) return document.documentElement;
            for (var e = t.parentElement; e && "none" === r(e, "transform");) e = e.parentElement;
            return e || document.documentElement
        }

        function k(t, e, i, n) {
            var o = arguments.length > 4 && void 0 !== arguments[4] && arguments[4],
                s = {
                    top: 0,
                    left: 0
                },
                r = o ? T(t) : p(t, e);
            if ("viewport" === n) s = S(r, o);
            else {
                var c = void 0;
                "scrollParent" === n ? (c = l(a(e)), "BODY" === c.nodeName && (c = t.ownerDocument.documentElement)) : c = "window" === n ? t.ownerDocument.documentElement : n;
                var d = b(c, r, o);
                if ("HTML" !== c.nodeName || x(r)) s = d;
                else {
                    var u = y(t.ownerDocument),
                        f = u.height,
                        h = u.width;
                    s.top += d.top - d.marginTop, s.bottom = f + d.top, s.left += d.left - d.marginLeft, s.right = h + d.left
                }
            }
            i = i || 0;
            var m = "number" == typeof i;
            return s.left += m ? i : i.left || 0, s.top += m ? i : i.top || 0, s.right -= m ? i : i.right || 0, s.bottom -= m ? i : i.bottom || 0, s
        }

        function C(t) {
            return t.width * t.height
        }

        function E(t, e, i, n, o) {
            var s = arguments.length > 5 && void 0 !== arguments[5] ? arguments[5] : 0;
            if (-1 === t.indexOf("auto")) return t;
            var r = k(i, n, s, o),
                a = {
                    top: {
                        width: r.width,
                        height: e.top - r.top
                    },
                    right: {
                        width: r.right - e.right,
                        height: r.height
                    },
                    bottom: {
                        width: r.width,
                        height: r.bottom - e.bottom
                    },
                    left: {
                        width: e.left - r.left,
                        height: r.height
                    }
                },
                l = Object.keys(a).map(function(t) {
                    return St({
                        key: t
                    }, a[t], {
                        area: C(a[t])
                    })
                }).sort(function(t, e) {
                    return e.area - t.area
                }),
                c = l.filter(function(t) {
                    var e = t.width,
                        n = t.height;
                    return e >= i.clientWidth && n >= i.clientHeight
                }),
                d = c.length > 0 ? c[0].key : l[0].key,
                u = t.split("-")[1];
            return d + (u ? "-" + u : "")
        }

        function A(t, e, i) {
            var n = arguments.length > 3 && void 0 !== arguments[3] ? arguments[3] : null;
            return b(i, n ? T(e) : p(e, i), n)
        }

        function O(t) {
            var e = t.ownerDocument.defaultView,
                i = e.getComputedStyle(t),
                n = parseFloat(i.marginTop || 0) + parseFloat(i.marginBottom || 0),
                o = parseFloat(i.marginLeft || 0) + parseFloat(i.marginRight || 0);
            return {
                width: t.offsetWidth + o,
                height: t.offsetHeight + n
            }
        }

        function I(t) {
            var e = {
                left: "right",
                right: "left",
                bottom: "top",
                top: "bottom"
            };
            return t.replace(/left|right|bottom|top/g, function(t) {
                return e[t]
            })
        }

        function $(t, e, i) {
            i = i.split("-")[0];
            var n = O(t),
                o = {
                    width: n.width,
                    height: n.height
                },
                s = -1 !== ["right", "left"].indexOf(i),
                r = s ? "top" : "left",
                a = s ? "left" : "top",
                l = s ? "height" : "width",
                c = s ? "width" : "height";
            return o[r] = e[r] + e[l] / 2 - n[l] / 2, o[a] = i === a ? e[a] - n[c] : e[I(a)], o
        }

        function D(t, e) {
            return Array.prototype.find ? t.find(e) : t.filter(e)[0]
        }

        function N(t, e, i) {
            if (Array.prototype.findIndex) return t.findIndex(function(t) {
                return t[e] === i
            });
            var n = D(t, function(t) {
                return t[e] === i
            });
            return t.indexOf(n)
        }

        function L(t, e, i) {
            return (void 0 === i ? t : t.slice(0, N(t, "name", i))).forEach(function(t) {
                t.function;
                var i = t.function || t.fn;
                t.enabled && s(i) && (e.offsets.popper = w(e.offsets.popper), e.offsets.reference = w(e.offsets.reference), e = i(e, t))
            }), e
        }

        function P() {
            if (!this.state.isDestroyed) {
                var t = {
                    instance: this,
                    styles: {},
                    arrowStyles: {},
                    attributes: {},
                    flipped: !1,
                    offsets: {}
                };
                t.offsets.reference = A(this.state, this.popper, this.reference, this.options.positionFixed), t.placement = E(this.options.placement, t.offsets.reference, this.popper, this.reference, this.options.modifiers.flip.boundariesElement, this.options.modifiers.flip.padding), t.originalPlacement = t.placement, t.positionFixed = this.options.positionFixed, t.offsets.popper = $(this.popper, t.offsets.reference, t.placement), t.offsets.popper.position = this.options.positionFixed ? "fixed" : "absolute", t = L(this.modifiers, t), this.state.isCreated ? this.options.onUpdate(t) : (this.state.isCreated = !0, this.options.onCreate(t))
            }
        }

        function j(t, e) {
            return t.some(function(t) {
                var i = t.name;
                return t.enabled && i === e
            })
        }

        function H(t) {
            for (var e = [!1, "ms", "Webkit", "Moz", "O"], i = t.charAt(0).toUpperCase() + t.slice(1), n = 0; n < e.length; n++) {
                var o = e[n],
                    s = o ? "" + o + i : t;
                if (void 0 !== document.body.style[s]) return s
            }
            return null
        }

        function B() {
            return this.state.isDestroyed = !0, j(this.modifiers, "applyStyle") && (this.popper.removeAttribute("x-placement"), this.popper.style.position = "", this.popper.style.top = "", this.popper.style.left = "", this.popper.style.right = "", this.popper.style.bottom = "", this.popper.style.willChange = "", this.popper.style[H("transform")] = ""), this.disableEventListeners(), this.options.removeOnDestroy && this.popper.parentNode.removeChild(this.popper), this
        }

        function M(t) {
            var e = t.ownerDocument;
            return e ? e.defaultView : window
        }

        function F(t, e, i, n) {
            var o = "BODY" === t.nodeName,
                s = o ? t.ownerDocument.defaultView : t;
            s.addEventListener(e, i, {
                passive: !0
            }), o || F(l(s.parentNode), e, i, n), n.push(s)
        }

        function z(t, e, i, n) {
            i.updateBound = n, M(t).addEventListener("resize", i.updateBound, {
                passive: !0
            });
            var o = l(t);
            return F(o, "scroll", i.updateBound, i.scrollParents), i.scrollElement = o, i.eventsEnabled = !0, i
        }

        function W() {
            this.state.eventsEnabled || (this.state = z(this.reference, this.options, this.state, this.scheduleUpdate))
        }

        function q(t, e) {
            return M(t).removeEventListener("resize", e.updateBound), e.scrollParents.forEach(function(t) {
                t.removeEventListener("scroll", e.updateBound)
            }), e.updateBound = null, e.scrollParents = [], e.scrollElement = null, e.eventsEnabled = !1, e
        }

        function R() {
            this.state.eventsEnabled && (cancelAnimationFrame(this.scheduleUpdate), this.state = q(this.reference, this.state))
        }

        function U(t) {
            return "" !== t && !isNaN(parseFloat(t)) && isFinite(t)
        }

        function Q(t, e) {
            Object.keys(e).forEach(function(i) {
                var n = ""; - 1 !== ["width", "height", "top", "right", "bottom", "left"].indexOf(i) && U(e[i]) && (n = "px"), t.style[i] = e[i] + n
            })
        }

        function K(t, e) {
            Object.keys(e).forEach(function(i) {
                !1 !== e[i] ? t.setAttribute(i, e[i]) : t.removeAttribute(i)
            })
        }

        function Y(t) {
            return Q(t.instance.popper, t.styles), K(t.instance.popper, t.attributes), t.arrowElement && Object.keys(t.arrowStyles).length && Q(t.arrowElement, t.arrowStyles), t
        }

        function V(t, e, i, n, o) {
            var s = A(o, e, t, i.positionFixed),
                r = E(i.placement, s, e, t, i.modifiers.flip.boundariesElement, i.modifiers.flip.padding);
            return e.setAttribute("x-placement", r), Q(e, {
                position: i.positionFixed ? "fixed" : "absolute"
            }), i
        }

        function X(t, e) {
            var i = t.offsets,
                n = i.popper,
                o = i.reference,
                s = -1 !== ["left", "right"].indexOf(t.placement),
                r = -1 !== t.placement.indexOf("-"),
                a = o.width % 2 == n.width % 2,
                l = o.width % 2 == 1 && n.width % 2 == 1,
                c = function(t) {
                    return t
                },
                d = e ? s || r || a ? Math.round : Math.floor : c,
                u = e ? Math.round : c;
            return {
                left: d(l && !r && e ? n.left - 1 : n.left),
                top: u(n.top),
                bottom: u(n.bottom),
                right: d(n.right)
            }
        }

        function G(t, e) {
            var i = e.x,
                n = e.y,
                o = t.offsets.popper,
                s = D(t.instance.modifiers, function(t) {
                    return "applyStyle" === t.name
                }).gpuAcceleration,
                r = void 0 !== s ? s : e.gpuAcceleration,
                a = d(t.instance.popper),
                l = _(a),
                c = {
                    position: o.position
                },
                u = X(t, window.devicePixelRatio < 2 || !xt),
                f = "bottom" === i ? "top" : "bottom",
                p = "right" === n ? "left" : "right",
                h = H("transform"),
                m = void 0,
                g = void 0;
            if (g = "bottom" === f ? "HTML" === a.nodeName ? -a.clientHeight + u.bottom : -l.height + u.bottom : u.top, m = "right" === p ? "HTML" === a.nodeName ? -a.clientWidth + u.right : -l.width + u.right : u.left, r && h) c[h] = "translate3d(" + m + "px, " + g + "px, 0)", c[f] = 0, c[p] = 0, c.willChange = "transform";
            else {
                var v = "bottom" === f ? -1 : 1,
                    y = "right" === p ? -1 : 1;
                c[f] = g * v, c[p] = m * y, c.willChange = f + ", " + p
            }
            var w = {
                "x-placement": t.placement
            };
            return t.attributes = St({}, w, t.attributes), t.styles = St({}, c, t.styles), t.arrowStyles = St({}, t.offsets.arrow, t.arrowStyles), t
        }

        function Z(t, e, i) {
            var n = D(t, function(t) {
                    return t.name === e
                }),
                o = !!n && t.some(function(t) {
                    return t.name === i && t.enabled && t.order < n.order
                });
            if (!o);
            return o
        }

        function J(t, e) {
            var i;
            if (!Z(t.instance.modifiers, "arrow", "keepTogether")) return t;
            var n = e.element;
            if ("string" == typeof n) {
                if (!(n = t.instance.popper.querySelector(n))) return t
            } else if (!t.instance.popper.contains(n)) return t;
            var o = t.placement.split("-")[0],
                s = t.offsets,
                a = s.popper,
                l = s.reference,
                c = -1 !== ["left", "right"].indexOf(o),
                d = c ? "height" : "width",
                u = c ? "Top" : "Left",
                f = u.toLowerCase(),
                p = c ? "left" : "top",
                h = c ? "bottom" : "right",
                m = O(n)[d];
            l[h] - m < a[f] && (t.offsets.popper[f] -= a[f] - (l[h] - m)), l[f] + m > a[h] && (t.offsets.popper[f] += l[f] + m - a[h]), t.offsets.popper = w(t.offsets.popper);
            var g = l[f] + l[d] / 2 - m / 2,
                v = r(t.instance.popper),
                y = parseFloat(v["margin" + u], 10),
                _ = parseFloat(v["border" + u + "Width"], 10),
                b = g - t.offsets.popper[f] - y - _;
            return b = Math.max(Math.min(a[d] - m, b), 0), t.arrowElement = n, t.offsets.arrow = (i = {}, bt(i, f, Math.round(b)), bt(i, p, ""), i), t
        }

        function tt(t) {
            return "end" === t ? "start" : "start" === t ? "end" : t
        }

        function et(t) {
            var e = arguments.length > 1 && void 0 !== arguments[1] && arguments[1],
                i = kt.indexOf(t),
                n = kt.slice(i + 1).concat(kt.slice(0, i));
            return e ? n.reverse() : n
        }

        function it(t, e) {
            if (j(t.instance.modifiers, "inner")) return t;
            if (t.flipped && t.placement === t.originalPlacement) return t;
            var i = k(t.instance.popper, t.instance.reference, e.padding, e.boundariesElement, t.positionFixed),
                n = t.placement.split("-")[0],
                o = I(n),
                s = t.placement.split("-")[1] || "",
                r = [];
            switch (e.behavior) {
                case Ct.FLIP:
                    r = [n, o];
                    break;
                case Ct.CLOCKWISE:
                    r = et(n);
                    break;
                case Ct.COUNTERCLOCKWISE:
                    r = et(n, !0);
                    break;
                default:
                    r = e.behavior
            }
            return r.forEach(function(a, l) {
                if (n !== a || r.length === l + 1) return t;
                n = t.placement.split("-")[0], o = I(n);
                var c = t.offsets.popper,
                    d = t.offsets.reference,
                    u = Math.floor,
                    f = "left" === n && u(c.right) > u(d.left) || "right" === n && u(c.left) < u(d.right) || "top" === n && u(c.bottom) > u(d.top) || "bottom" === n && u(c.top) < u(d.bottom),
                    p = u(c.left) < u(i.left),
                    h = u(c.right) > u(i.right),
                    m = u(c.top) < u(i.top),
                    g = u(c.bottom) > u(i.bottom),
                    v = "left" === n && p || "right" === n && h || "top" === n && m || "bottom" === n && g,
                    y = -1 !== ["top", "bottom"].indexOf(n),
                    w = !!e.flipVariations && (y && "start" === s && p || y && "end" === s && h || !y && "start" === s && m || !y && "end" === s && g);
                (f || v || w) && (t.flipped = !0, (f || v) && (n = r[l + 1]), w && (s = tt(s)), t.placement = n + (s ? "-" + s : ""), t.offsets.popper = St({}, t.offsets.popper, $(t.instance.popper, t.offsets.reference, t.placement)), t = L(t.instance.modifiers, t, "flip"))
            }), t
        }

        function nt(t) {
            var e = t.offsets,
                i = e.popper,
                n = e.reference,
                o = t.placement.split("-")[0],
                s = Math.floor,
                r = -1 !== ["top", "bottom"].indexOf(o),
                a = r ? "right" : "bottom",
                l = r ? "left" : "top",
                c = r ? "width" : "height";
            return i[a] < s(n[l]) && (t.offsets.popper[l] = s(n[l]) - i[c]), i[l] > s(n[a]) && (t.offsets.popper[l] = s(n[a])), t
        }

        function ot(t, e, i, n) {
            var o = t.match(/((?:\-|\+)?\d*\.?\d*)(.*)/),
                s = +o[1],
                r = o[2];
            if (!s) return t;
            if (0 === r.indexOf("%")) {
                var a = void 0;
                switch (r) {
                    case "%p":
                        a = i;
                        break;
                    case "%":
                    case "%r":
                    default:
                        a = n
                }
                return w(a)[e] / 100 * s
            }
            if ("vh" === r || "vw" === r) {
                return ("vh" === r ? Math.max(document.documentElement.clientHeight, window.innerHeight || 0) : Math.max(document.documentElement.clientWidth, window.innerWidth || 0)) / 100 * s
            }
            return s
        }

        function st(t, e, i, n) {
            var o = [0, 0],
                s = -1 !== ["right", "left"].indexOf(n),
                r = t.split(/(\+|\-)/).map(function(t) {
                    return t.trim()
                }),
                a = r.indexOf(D(r, function(t) {
                    return -1 !== t.search(/,|\s/)
                }));
            r[a] && r[a].indexOf(",");
            var l = /\s*,\s*|\s+/,
                c = -1 !== a ? [r.slice(0, a).concat([r[a].split(l)[0]]), [r[a].split(l)[1]].concat(r.slice(a + 1))] : [r];
            return c = c.map(function(t, n) {
                var o = (1 === n ? !s : s) ? "height" : "width",
                    r = !1;
                return t.reduce(function(t, e) {
                    return "" === t[t.length - 1] && -1 !== ["+", "-"].indexOf(e) ? (t[t.length - 1] = e, r = !0, t) : r ? (t[t.length - 1] += e, r = !1, t) : t.concat(e)
                }, []).map(function(t) {
                    return ot(t, o, e, i)
                })
            }), c.forEach(function(t, e) {
                t.forEach(function(i, n) {
                    U(i) && (o[e] += i * ("-" === t[n - 1] ? -1 : 1))
                })
            }), o
        }

        function rt(t, e) {
            var i = e.offset,
                n = t.placement,
                o = t.offsets,
                s = o.popper,
                r = o.reference,
                a = n.split("-")[0],
                l = void 0;
            return l = U(+i) ? [+i, 0] : st(i, s, r, a), "left" === a ? (s.top += l[0], s.left -= l[1]) : "right" === a ? (s.top += l[0], s.left += l[1]) : "top" === a ? (s.left += l[0], s.top -= l[1]) : "bottom" === a && (s.left += l[0], s.top += l[1]), t.popper = s, t
        }

        function at(t, e) {
            var i = e.boundariesElement || d(t.instance.popper);
            t.instance.reference === i && (i = d(i));
            var n = H("transform"),
                o = t.instance.popper.style,
                s = o.top,
                r = o.left,
                a = o[n];
            o.top = "", o.left = "", o[n] = "";
            var l = k(t.instance.popper, t.instance.reference, e.padding, i, t.positionFixed);
            o.top = s, o.left = r, o[n] = a, e.boundaries = l;
            var c = e.priority,
                u = t.offsets.popper,
                f = {
                    primary: function(t) {
                        var i = u[t];
                        return u[t] < l[t] && !e.escapeWithReference && (i = Math.max(u[t], l[t])), bt({}, t, i)
                    },
                    secondary: function(t) {
                        var i = "right" === t ? "left" : "top",
                            n = u[i];
                        return u[t] > l[t] && !e.escapeWithReference && (n = Math.min(u[i], l[t] - ("right" === t ? u.width : u.height))), bt({}, i, n)
                    }
                };
            return c.forEach(function(t) {
                var e = -1 !== ["left", "top"].indexOf(t) ? "primary" : "secondary";
                u = St({}, u, f[e](t))
            }), t.offsets.popper = u, t
        }

        function lt(t) {
            var e = t.placement,
                i = e.split("-")[0],
                n = e.split("-")[1];
            if (n) {
                var o = t.offsets,
                    s = o.reference,
                    r = o.popper,
                    a = -1 !== ["bottom", "top"].indexOf(i),
                    l = a ? "left" : "top",
                    c = a ? "width" : "height",
                    d = {
                        start: bt({}, l, s[l]),
                        end: bt({}, l, s[l] + s[c] - r[c])
                    };
                t.offsets.popper = St({}, r, d[n])
            }
            return t
        }

        function ct(t) {
            if (!Z(t.instance.modifiers, "hide", "preventOverflow")) return t;
            var e = t.offsets.reference,
                i = D(t.instance.modifiers, function(t) {
                    return "preventOverflow" === t.name
                }).boundaries;
            if (e.bottom < i.top || e.left > i.right || e.top > i.bottom || e.right < i.left) {
                if (!0 === t.hide) return t;
                t.hide = !0, t.attributes["x-out-of-boundaries"] = ""
            } else {
                if (!1 === t.hide) return t;
                t.hide = !1, t.attributes["x-out-of-boundaries"] = !1
            }
            return t
        }

        function dt(t) {
            var e = t.placement,
                i = e.split("-")[0],
                n = t.offsets,
                o = n.popper,
                s = n.reference,
                r = -1 !== ["left", "right"].indexOf(i),
                a = -1 === ["top", "left"].indexOf(i);
            return o[r ? "left" : "top"] = s[i] - (a ? o[r ? "width" : "height"] : 0), t.placement = I(e), t.offsets.popper = w(o), t
        }
        Object.defineProperty(e, "__esModule", {
            value: !0
        });
        for (var ut = "undefined" != typeof window && "undefined" != typeof document, ft = ["Edge", "Trident", "Firefox"], pt = 0, ht = 0; ht < ft.length; ht += 1)
            if (ut && navigator.userAgent.indexOf(ft[ht]) >= 0) {
                pt = 1;
                break
            } var mt = ut && window.Promise,
            gt = mt ? n : o,
            vt = ut && !(!window.MSInputMethodContext || !document.documentMode),
            yt = ut && /MSIE 10/.test(navigator.userAgent),
            wt = function(t, e) {
                if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
            },
            _t = function() {
                function t(t, e) {
                    for (var i = 0; i < e.length; i++) {
                        var n = e[i];
                        n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                    }
                }
                return function(e, i, n) {
                    return i && t(e.prototype, i), n && t(e, n), e
                }
            }(),
            bt = function(t, e, i) {
                return e in t ? Object.defineProperty(t, e, {
                    value: i,
                    enumerable: !0,
                    configurable: !0,
                    writable: !0
                }) : t[e] = i, t
            },
            St = Object.assign || function(t) {
                for (var e = 1; e < arguments.length; e++) {
                    var i = arguments[e];
                    for (var n in i) Object.prototype.hasOwnProperty.call(i, n) && (t[n] = i[n])
                }
                return t
            },
            xt = ut && /Firefox/i.test(navigator.userAgent),
            Tt = ["auto-start", "auto", "auto-end", "top-start", "top", "top-end", "right-start", "right", "right-end", "bottom-end", "bottom", "bottom-start", "left-end", "left", "left-start"],
            kt = Tt.slice(3),
            Ct = {
                FLIP: "flip",
                CLOCKWISE: "clockwise",
                COUNTERCLOCKWISE: "counterclockwise"
            },
            Et = {
                shift: {
                    order: 100,
                    enabled: !0,
                    fn: lt
                },
                offset: {
                    order: 200,
                    enabled: !0,
                    fn: rt,
                    offset: 0
                },
                preventOverflow: {
                    order: 300,
                    enabled: !0,
                    fn: at,
                    priority: ["left", "right", "top", "bottom"],
                    padding: 5,
                    boundariesElement: "scrollParent"
                },
                keepTogether: {
                    order: 400,
                    enabled: !0,
                    fn: nt
                },
                arrow: {
                    order: 500,
                    enabled: !0,
                    fn: J,
                    element: "[x-arrow]"
                },
                flip: {
                    order: 600,
                    enabled: !0,
                    fn: it,
                    behavior: "flip",
                    padding: 5,
                    boundariesElement: "viewport"
                },
                inner: {
                    order: 700,
                    enabled: !1,
                    fn: dt
                },
                hide: {
                    order: 800,
                    enabled: !0,
                    fn: ct
                },
                computeStyle: {
                    order: 850,
                    enabled: !0,
                    fn: G,
                    gpuAcceleration: !0,
                    x: "bottom",
                    y: "right"
                },
                applyStyle: {
                    order: 900,
                    enabled: !0,
                    fn: Y,
                    onLoad: V,
                    gpuAcceleration: void 0
                }
            },
            At = {
                placement: "bottom",
                positionFixed: !1,
                eventsEnabled: !0,
                removeOnDestroy: !1,
                onCreate: function() {},
                onUpdate: function() {},
                modifiers: Et
            },
            Ot = function() {
                function t(e, i) {
                    var n = this,
                        o = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : {};
                    wt(this, t), this.scheduleUpdate = function() {
                        return requestAnimationFrame(n.update)
                    }, this.update = gt(this.update.bind(this)), this.options = St({}, t.Defaults, o), this.state = {
                        isDestroyed: !1,
                        isCreated: !1,
                        scrollParents: []
                    }, this.reference = e && e.jquery ? e[0] : e, this.popper = i && i.jquery ? i[0] : i, this.options.modifiers = {}, Object.keys(St({}, t.Defaults.modifiers, o.modifiers)).forEach(function(e) {
                        n.options.modifiers[e] = St({}, t.Defaults.modifiers[e] || {}, o.modifiers ? o.modifiers[e] : {})
                    }), this.modifiers = Object.keys(this.options.modifiers).map(function(t) {
                        return St({
                            name: t
                        }, n.options.modifiers[t])
                    }).sort(function(t, e) {
                        return t.order - e.order
                    }), this.modifiers.forEach(function(t) {
                        t.enabled && s(t.onLoad) && t.onLoad(n.reference, n.popper, n.options, t, n.state)
                    }), this.update();
                    var r = this.options.eventsEnabled;
                    r && this.enableEventListeners(), this.state.eventsEnabled = r
                }
                return _t(t, [{
                    key: "update",
                    value: function() {
                        return P.call(this)
                    }
                }, {
                    key: "destroy",
                    value: function() {
                        return B.call(this)
                    }
                }, {
                    key: "enableEventListeners",
                    value: function() {
                        return W.call(this)
                    }
                }, {
                    key: "disableEventListeners",
                    value: function() {
                        return R.call(this)
                    }
                }]), t
            }();
        Ot.Utils = ("undefined" != typeof window ? window : i).PopperUtils, Ot.placements = Tt, Ot.Defaults = At, e.default = Ot, t.exports = e.default
    }).call(e, i(36))
}, function(t, e, i) {
    "use strict";
    var n, o, s;
    ! function(r) {
        o = [i(0)], n = r, void 0 !== (s = "function" == typeof n ? n.apply(e, o) : n) && (t.exports = s)
    }(function(t) {
        var e = window.Slick || {};
        e = function() {
            function e(e, n) {
                var o, s = this;
                s.defaults = {
                    accessibility: !0,
                    adaptiveHeight: !1,
                    appendArrows: t(e),
                    appendDots: t(e),
                    arrows: !0,
                    asNavFor: null,
                    prevArrow: '<button class="slick-prev" aria-label="Previous" type="button">Previous</button>',
                    nextArrow: '<button class="slick-next" aria-label="Next" type="button">Next</button>',
                    autoplay: !1,
                    autoplaySpeed: 3e3,
                    centerMode: !1,
                    centerPadding: "50px",
                    cssEase: "ease",
                    customPaging: function(e, i) {
                        return t('<button type="button" />').text(i + 1)
                    },
                    dots: !1,
                    dotsClass: "slick-dots",
                    draggable: !0,
                    easing: "linear",
                    edgeFriction: .35,
                    fade: !1,
                    focusOnSelect: !1,
                    focusOnChange: !1,
                    infinite: !0,
                    initialSlide: 0,
                    lazyLoad: "ondemand",
                    mobileFirst: !1,
                    pauseOnHover: !0,
                    pauseOnFocus: !0,
                    pauseOnDotsHover: !1,
                    respondTo: "window",
                    responsive: null,
                    rows: 1,
                    rtl: !1,
                    slide: "",
                    slidesPerRow: 1,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    speed: 500,
                    swipe: !0,
                    swipeToSlide: !1,
                    touchMove: !0,
                    touchThreshold: 5,
                    useCSS: !0,
                    useTransform: !0,
                    variableWidth: !1,
                    vertical: !1,
                    verticalSwiping: !1,
                    waitForAnimate: !0,
                    zIndex: 1e3
                }, s.initials = {
                    animating: !1,
                    dragging: !1,
                    autoPlayTimer: null,
                    currentDirection: 0,
                    currentLeft: null,
                    currentSlide: 0,
                    direction: 1,
                    $dots: null,
                    listWidth: null,
                    listHeight: null,
                    loadIndex: 0,
                    $nextArrow: null,
                    $prevArrow: null,
                    scrolling: !1,
                    slideCount: null,
                    slideWidth: null,
                    $slideTrack: null,
                    $slides: null,
                    sliding: !1,
                    slideOffset: 0,
                    swipeLeft: null,
                    swiping: !1,
                    $list: null,
                    touchObject: {},
                    transformsEnabled: !1,
                    unslicked: !1
                }, t.extend(s, s.initials), s.activeBreakpoint = null, s.animType = null, s.animProp = null, s.breakpoints = [], s.breakpointSettings = [], s.cssTransitions = !1, s.focussed = !1, s.interrupted = !1, s.hidden = "hidden", s.paused = !0, s.positionProp = null, s.respondTo = null, s.rowCount = 1, s.shouldClick = !0, s.$slider = t(e), s.$slidesCache = null, s.transformType = null, s.transitionType = null, s.visibilityChange = "visibilitychange", s.windowWidth = 0, s.windowTimer = null, o = t(e).data("slick") || {}, s.options = t.extend({}, s.defaults, n, o), s.currentSlide = s.options.initialSlide, s.originalSettings = s.options, void 0 !== document.mozHidden ? (s.hidden = "mozHidden", s.visibilityChange = "mozvisibilitychange") : void 0 !== document.webkitHidden && (s.hidden = "webkitHidden", s.visibilityChange = "webkitvisibilitychange"), s.autoPlay = t.proxy(s.autoPlay, s), s.autoPlayClear = t.proxy(s.autoPlayClear, s), s.autoPlayIterator = t.proxy(s.autoPlayIterator, s), s.changeSlide = t.proxy(s.changeSlide, s), s.clickHandler = t.proxy(s.clickHandler, s), s.selectHandler = t.proxy(s.selectHandler, s), s.setPosition = t.proxy(s.setPosition, s), s.swipeHandler = t.proxy(s.swipeHandler, s), s.dragHandler = t.proxy(s.dragHandler, s), s.keyHandler = t.proxy(s.keyHandler, s), s.instanceUid = i++, s.htmlExpr = /^(?:\s*(<[\w\W]+>)[^>]*)$/, s.registerBreakpoints(), s.init(!0)
            }
            var i = 0;
            return e
        }(), e.prototype.activateADA = function() {
            this.$slideTrack.find(".slick-active").attr({
                "aria-hidden": "false"
            }).find("a, input, button, select").attr({
                tabindex: "0"
            })
        }, e.prototype.addSlide = e.prototype.slickAdd = function(e, i, n) {
            var o = this;
            if ("boolean" == typeof i) n = i, i = null;
            else if (i < 0 || i >= o.slideCount) return !1;
            o.unload(), "number" == typeof i ? 0 === i && 0 === o.$slides.length ? t(e).appendTo(o.$slideTrack) : n ? t(e).insertBefore(o.$slides.eq(i)) : t(e).insertAfter(o.$slides.eq(i)) : !0 === n ? t(e).prependTo(o.$slideTrack) : t(e).appendTo(o.$slideTrack), o.$slides = o.$slideTrack.children(this.options.slide), o.$slideTrack.children(this.options.slide).detach(), o.$slideTrack.append(o.$slides), o.$slides.each(function(e, i) {
                t(i).attr("data-slick-index", e)
            }), o.$slidesCache = o.$slides, o.reinit()
        }, e.prototype.animateHeight = function() {
            var t = this;
            if (1 === t.options.slidesToShow && !0 === t.options.adaptiveHeight && !1 === t.options.vertical) {
                var e = t.$slides.eq(t.currentSlide).outerHeight(!0);
                t.$list.animate({
                    height: e
                }, t.options.speed)
            }
        }, e.prototype.animateSlide = function(e, i) {
            var n = {},
                o = this;
            o.animateHeight(), !0 === o.options.rtl && !1 === o.options.vertical && (e = -e), !1 === o.transformsEnabled ? !1 === o.options.vertical ? o.$slideTrack.animate({
                left: e
            }, o.options.speed, o.options.easing, i) : o.$slideTrack.animate({
                top: e
            }, o.options.speed, o.options.easing, i) : !1 === o.cssTransitions ? (!0 === o.options.rtl && (o.currentLeft = -o.currentLeft), t({
                animStart: o.currentLeft
            }).animate({
                animStart: e
            }, {
                duration: o.options.speed,
                easing: o.options.easing,
                step: function(t) {
                    t = Math.ceil(t), !1 === o.options.vertical ? (n[o.animType] = "translate(" + t + "px, 0px)", o.$slideTrack.css(n)) : (n[o.animType] = "translate(0px," + t + "px)", o.$slideTrack.css(n))
                },
                complete: function() {
                    i && i.call()
                }
            })) : (o.applyTransition(), e = Math.ceil(e), !1 === o.options.vertical ? n[o.animType] = "translate3d(" + e + "px, 0px, 0px)" : n[o.animType] = "translate3d(0px," + e + "px, 0px)", o.$slideTrack.css(n), i && setTimeout(function() {
                o.disableTransition(), i.call()
            }, o.options.speed))
        }, e.prototype.getNavTarget = function() {
            var e = this,
                i = e.options.asNavFor;
            return i && null !== i && (i = t(i).not(e.$slider)), i
        }, e.prototype.asNavFor = function(e) {
            var i = this,
                n = i.getNavTarget();
            null !== n && "object" == typeof n && n.each(function() {
                var i = t(this).slick("getSlick");
                i.unslicked || i.slideHandler(e, !0)
            })
        }, e.prototype.applyTransition = function(t) {
            var e = this,
                i = {};
            !1 === e.options.fade ? i[e.transitionType] = e.transformType + " " + e.options.speed + "ms " + e.options.cssEase : i[e.transitionType] = "opacity " + e.options.speed + "ms " + e.options.cssEase, !1 === e.options.fade ? e.$slideTrack.css(i) : e.$slides.eq(t).css(i)
        }, e.prototype.autoPlay = function() {
            var t = this;
            t.autoPlayClear(), t.slideCount > t.options.slidesToShow && (t.autoPlayTimer = setInterval(t.autoPlayIterator, t.options.autoplaySpeed))
        }, e.prototype.autoPlayClear = function() {
            var t = this;
            t.autoPlayTimer && clearInterval(t.autoPlayTimer)
        }, e.prototype.autoPlayIterator = function() {
            var t = this,
                e = t.currentSlide + t.options.slidesToScroll;
            t.paused || t.interrupted || t.focussed || (!1 === t.options.infinite && (1 === t.direction && t.currentSlide + 1 === t.slideCount - 1 ? t.direction = 0 : 0 === t.direction && (e = t.currentSlide - t.options.slidesToScroll, t.currentSlide - 1 == 0 && (t.direction = 1))), t.slideHandler(e))
        }, e.prototype.buildArrows = function() {
            var e = this;
            !0 === e.options.arrows && (e.$prevArrow = t(e.options.prevArrow).addClass("slick-arrow"), e.$nextArrow = t(e.options.nextArrow).addClass("slick-arrow"), e.slideCount > e.options.slidesToShow ? (e.$prevArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"), e.$nextArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"), e.htmlExpr.test(e.options.prevArrow) && e.$prevArrow.prependTo(e.options.appendArrows), e.htmlExpr.test(e.options.nextArrow) && e.$nextArrow.appendTo(e.options.appendArrows), !0 !== e.options.infinite && e.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true")) : e.$prevArrow.add(e.$nextArrow).addClass("slick-hidden").attr({
                "aria-disabled": "true",
                tabindex: "-1"
            }))
        }, e.prototype.buildDots = function() {
            var e, i, n = this;
            if (!0 === n.options.dots && n.slideCount > n.options.slidesToShow) {
                for (n.$slider.addClass("slick-dotted"), i = t("<ul />").addClass(n.options.dotsClass), e = 0; e <= n.getDotCount(); e += 1) i.append(t("<li />").append(n.options.customPaging.call(this, n, e)));
                n.$dots = i.appendTo(n.options.appendDots), n.$dots.find("li").first().addClass("slick-active")
            }
        }, e.prototype.buildOut = function() {
            var e = this;
            e.$slides = e.$slider.children(e.options.slide + ":not(.slick-cloned)").addClass("slick-slide"), e.slideCount = e.$slides.length, e.$slides.each(function(e, i) {
                t(i).attr("data-slick-index", e).data("originalStyling", t(i).attr("style") || "")
            }), e.$slider.addClass("slick-slider"), e.$slideTrack = 0 === e.slideCount ? t('<div class="slick-track"/>').appendTo(e.$slider) : e.$slides.wrapAll('<div class="slick-track"/>').parent(), e.$list = e.$slideTrack.wrap('<div class="slick-list"/>').parent(), e.$slideTrack.css("opacity", 0), !0 !== e.options.centerMode && !0 !== e.options.swipeToSlide || (e.options.slidesToScroll = 1), t("img[data-lazy]", e.$slider).not("[src]").addClass("slick-loading"), e.setupInfinite(), e.buildArrows(), e.buildDots(), e.updateDots(), e.setSlideClasses("number" == typeof e.currentSlide ? e.currentSlide : 0), !0 === e.options.draggable && e.$list.addClass("draggable")
        }, e.prototype.buildRows = function() {
            var t, e, i, n, o, s, r, a = this;
            if (n = document.createDocumentFragment(), s = a.$slider.children(), a.options.rows > 0) {
                for (r = a.options.slidesPerRow * a.options.rows, o = Math.ceil(s.length / r), t = 0; t < o; t++) {
                    var l = document.createElement("div");
                    for (e = 0; e < a.options.rows; e++) {
                        var c = document.createElement("div");
                        for (i = 0; i < a.options.slidesPerRow; i++) {
                            var d = t * r + (e * a.options.slidesPerRow + i);
                            s.get(d) && c.appendChild(s.get(d))
                        }
                        l.appendChild(c)
                    }
                    n.appendChild(l)
                }
                a.$slider.empty().append(n), a.$slider.children().children().children().css({
                    width: 100 / a.options.slidesPerRow + "%",
                    display: "inline-block"
                })
            }
        }, e.prototype.checkResponsive = function(e, i) {
            var n, o, s, r = this,
                a = !1,
                l = r.$slider.width(),
                c = window.innerWidth || t(window).width();
            if ("window" === r.respondTo ? s = c : "slider" === r.respondTo ? s = l : "min" === r.respondTo && (s = Math.min(c, l)), r.options.responsive && r.options.responsive.length && null !== r.options.responsive) {
                o = null;
                for (n in r.breakpoints) r.breakpoints.hasOwnProperty(n) && (!1 === r.originalSettings.mobileFirst ? s < r.breakpoints[n] && (o = r.breakpoints[n]) : s > r.breakpoints[n] && (o = r.breakpoints[n]));
                null !== o ? null !== r.activeBreakpoint ? (o !== r.activeBreakpoint || i) && (r.activeBreakpoint = o, "unslick" === r.breakpointSettings[o] ? r.unslick(o) : (r.options = t.extend({}, r.originalSettings, r.breakpointSettings[o]), !0 === e && (r.currentSlide = r.options.initialSlide), r.refresh(e)), a = o) : (r.activeBreakpoint = o, "unslick" === r.breakpointSettings[o] ? r.unslick(o) : (r.options = t.extend({}, r.originalSettings, r.breakpointSettings[o]), !0 === e && (r.currentSlide = r.options.initialSlide), r.refresh(e)), a = o) : null !== r.activeBreakpoint && (r.activeBreakpoint = null, r.options = r.originalSettings, !0 === e && (r.currentSlide = r.options.initialSlide), r.refresh(e), a = o), e || !1 === a || r.$slider.trigger("breakpoint", [r, a])
            }
        }, e.prototype.changeSlide = function(e, i) {
            var n, o, s, r = this,
                a = t(e.currentTarget);
            switch (a.is("a") && e.preventDefault(), a.is("li") || (a = a.closest("li")), s = r.slideCount % r.options.slidesToScroll != 0, n = s ? 0 : (r.slideCount - r.currentSlide) % r.options.slidesToScroll, e.data.message) {
                case "previous":
                    o = 0 === n ? r.options.slidesToScroll : r.options.slidesToShow - n, r.slideCount > r.options.slidesToShow && r.slideHandler(r.currentSlide - o, !1, i);
                    break;
                case "next":
                    o = 0 === n ? r.options.slidesToScroll : n, r.slideCount > r.options.slidesToShow && r.slideHandler(r.currentSlide + o, !1, i);
                    break;
                case "index":
                    var l = 0 === e.data.index ? 0 : e.data.index || a.index() * r.options.slidesToScroll;
                    r.slideHandler(r.checkNavigable(l), !1, i), a.children().trigger("focus");
                    break;
                default:
                    return
            }
        }, e.prototype.checkNavigable = function(t) {
            var e, i, n = this;
            if (e = n.getNavigableIndexes(), i = 0, t > e[e.length - 1]) t = e[e.length - 1];
            else
                for (var o in e) {
                    if (t < e[o]) {
                        t = i;
                        break
                    }
                    i = e[o]
                }
            return t
        }, e.prototype.cleanUpEvents = function() {
            var e = this;
            e.options.dots && null !== e.$dots && (t("li", e.$dots).off("click.slick", e.changeSlide).off("mouseenter.slick", t.proxy(e.interrupt, e, !0)).off("mouseleave.slick", t.proxy(e.interrupt, e, !1)), !0 === e.options.accessibility && e.$dots.off("keydown.slick", e.keyHandler)), e.$slider.off("focus.slick blur.slick"), !0 === e.options.arrows && e.slideCount > e.options.slidesToShow && (e.$prevArrow && e.$prevArrow.off("click.slick", e.changeSlide), e.$nextArrow && e.$nextArrow.off("click.slick", e.changeSlide), !0 === e.options.accessibility && (e.$prevArrow && e.$prevArrow.off("keydown.slick", e.keyHandler), e.$nextArrow && e.$nextArrow.off("keydown.slick", e.keyHandler))), e.$list.off("touchstart.slick mousedown.slick", e.swipeHandler), e.$list.off("touchmove.slick mousemove.slick", e.swipeHandler), e.$list.off("touchend.slick mouseup.slick", e.swipeHandler), e.$list.off("touchcancel.slick mouseleave.slick", e.swipeHandler), e.$list.off("click.slick", e.clickHandler), t(document).off(e.visibilityChange, e.visibility), e.cleanUpSlideEvents(), !0 === e.options.accessibility && e.$list.off("keydown.slick", e.keyHandler), !0 === e.options.focusOnSelect && t(e.$slideTrack).children().off("click.slick", e.selectHandler), t(window).off("orientationchange.slick.slick-" + e.instanceUid, e.orientationChange), t(window).off("resize.slick.slick-" + e.instanceUid, e.resize), t("[draggable!=true]", e.$slideTrack).off("dragstart", e.preventDefault), t(window).off("load.slick.slick-" + e.instanceUid, e.setPosition)
        }, e.prototype.cleanUpSlideEvents = function() {
            var e = this;
            e.$list.off("mouseenter.slick", t.proxy(e.interrupt, e, !0)), e.$list.off("mouseleave.slick", t.proxy(e.interrupt, e, !1))
        }, e.prototype.cleanUpRows = function() {
            var t, e = this;
            e.options.rows > 0 && (t = e.$slides.children().children(), t.removeAttr("style"), e.$slider.empty().append(t))
        }, e.prototype.clickHandler = function(t) {
            !1 === this.shouldClick && (t.stopImmediatePropagation(), t.stopPropagation(), t.preventDefault())
        }, e.prototype.destroy = function(e) {
            var i = this;
            i.autoPlayClear(), i.touchObject = {}, i.cleanUpEvents(), t(".slick-cloned", i.$slider).detach(), i.$dots && i.$dots.remove(), i.$prevArrow && i.$prevArrow.length && (i.$prevArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), i.htmlExpr.test(i.options.prevArrow) && i.$prevArrow.remove()), i.$nextArrow && i.$nextArrow.length && (i.$nextArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), i.htmlExpr.test(i.options.nextArrow) && i.$nextArrow.remove()), i.$slides && (i.$slides.removeClass("slick-slide slick-active slick-center slick-visible slick-current").removeAttr("aria-hidden").removeAttr("data-slick-index").each(function() {
                t(this).attr("style", t(this).data("originalStyling"))
            }), i.$slideTrack.children(this.options.slide).detach(), i.$slideTrack.detach(), i.$list.detach(), i.$slider.append(i.$slides)), i.cleanUpRows(), i.$slider.removeClass("slick-slider"), i.$slider.removeClass("slick-initialized"), i.$slider.removeClass("slick-dotted"), i.unslicked = !0, e || i.$slider.trigger("destroy", [i])
        }, e.prototype.disableTransition = function(t) {
            var e = this,
                i = {};
            i[e.transitionType] = "", !1 === e.options.fade ? e.$slideTrack.css(i) : e.$slides.eq(t).css(i)
        }, e.prototype.fadeSlide = function(t, e) {
            var i = this;
            !1 === i.cssTransitions ? (i.$slides.eq(t).css({
                zIndex: i.options.zIndex
            }), i.$slides.eq(t).animate({
                opacity: 1
            }, i.options.speed, i.options.easing, e)) : (i.applyTransition(t), i.$slides.eq(t).css({
                opacity: 1,
                zIndex: i.options.zIndex
            }), e && setTimeout(function() {
                i.disableTransition(t), e.call()
            }, i.options.speed))
        }, e.prototype.fadeSlideOut = function(t) {
            var e = this;
            !1 === e.cssTransitions ? e.$slides.eq(t).animate({
                opacity: 0,
                zIndex: e.options.zIndex - 2
            }, e.options.speed, e.options.easing) : (e.applyTransition(t), e.$slides.eq(t).css({
                opacity: 0,
                zIndex: e.options.zIndex - 2
            }))
        }, e.prototype.filterSlides = e.prototype.slickFilter = function(t) {
            var e = this;
            null !== t && (e.$slidesCache = e.$slides, e.unload(), e.$slideTrack.children(this.options.slide).detach(), e.$slidesCache.filter(t).appendTo(e.$slideTrack), e.reinit())
        }, e.prototype.focusHandler = function() {
            var e = this;
            e.$slider.off("focus.slick blur.slick").on("focus.slick", "*", function(i) {
                var n = t(this);
                setTimeout(function() {
                    e.options.pauseOnFocus && n.is(":focus") && (e.focussed = !0, e.autoPlay())
                }, 0)
            }).on("blur.slick", "*", function(i) {
                t(this);
                e.options.pauseOnFocus && (e.focussed = !1, e.autoPlay())
            })
        }, e.prototype.getCurrent = e.prototype.slickCurrentSlide = function() {
            return this.currentSlide
        }, e.prototype.getDotCount = function() {
            var t = this,
                e = 0,
                i = 0,
                n = 0;
            if (!0 === t.options.infinite)
                if (t.slideCount <= t.options.slidesToShow) ++n;
                else
                    for (; e < t.slideCount;) ++n, e = i + t.options.slidesToScroll, i += t.options.slidesToScroll <= t.options.slidesToShow ? t.options.slidesToScroll : t.options.slidesToShow;
            else if (!0 === t.options.centerMode) n = t.slideCount;
            else if (t.options.asNavFor)
                for (; e < t.slideCount;) ++n, e = i + t.options.slidesToScroll, i += t.options.slidesToScroll <= t.options.slidesToShow ? t.options.slidesToScroll : t.options.slidesToShow;
            else n = 1 + Math.ceil((t.slideCount - t.options.slidesToShow) / t.options.slidesToScroll);
            return n - 1
        }, e.prototype.getLeft = function(t) {
            var e, i, n, o, s = this,
                r = 0;
            return s.slideOffset = 0, i = s.$slides.first().outerHeight(!0), !0 === s.options.infinite ? (s.slideCount > s.options.slidesToShow && (s.slideOffset = s.slideWidth * s.options.slidesToShow * -1, o = -1, !0 === s.options.vertical && !0 === s.options.centerMode && (2 === s.options.slidesToShow ? o = -1.5 : 1 === s.options.slidesToShow && (o = -2)), r = i * s.options.slidesToShow * o), s.slideCount % s.options.slidesToScroll != 0 && t + s.options.slidesToScroll > s.slideCount && s.slideCount > s.options.slidesToShow && (t > s.slideCount ? (s.slideOffset = (s.options.slidesToShow - (t - s.slideCount)) * s.slideWidth * -1, r = (s.options.slidesToShow - (t - s.slideCount)) * i * -1) : (s.slideOffset = s.slideCount % s.options.slidesToScroll * s.slideWidth * -1, r = s.slideCount % s.options.slidesToScroll * i * -1))) : t + s.options.slidesToShow > s.slideCount && (s.slideOffset = (t + s.options.slidesToShow - s.slideCount) * s.slideWidth, r = (t + s.options.slidesToShow - s.slideCount) * i), s.slideCount <= s.options.slidesToShow && (s.slideOffset = 0, r = 0), !0 === s.options.centerMode && s.slideCount <= s.options.slidesToShow ? s.slideOffset = s.slideWidth * Math.floor(s.options.slidesToShow) / 2 - s.slideWidth * s.slideCount / 2 : !0 === s.options.centerMode && !0 === s.options.infinite ? s.slideOffset += s.slideWidth * Math.floor(s.options.slidesToShow / 2) - s.slideWidth : !0 === s.options.centerMode && (s.slideOffset = 0, s.slideOffset += s.slideWidth * Math.floor(s.options.slidesToShow / 2)), e = !1 === s.options.vertical ? t * s.slideWidth * -1 + s.slideOffset : t * i * -1 + r, !0 === s.options.variableWidth && (n = s.slideCount <= s.options.slidesToShow || !1 === s.options.infinite ? s.$slideTrack.children(".slick-slide").eq(t) : s.$slideTrack.children(".slick-slide").eq(t + s.options.slidesToShow), e = !0 === s.options.rtl ? n[0] ? -1 * (s.$slideTrack.width() - n[0].offsetLeft - n.width()) : 0 : n[0] ? -1 * n[0].offsetLeft : 0, !0 === s.options.centerMode && (n = s.slideCount <= s.options.slidesToShow || !1 === s.options.infinite ? s.$slideTrack.children(".slick-slide").eq(t) : s.$slideTrack.children(".slick-slide").eq(t + s.options.slidesToShow + 1), e = !0 === s.options.rtl ? n[0] ? -1 * (s.$slideTrack.width() - n[0].offsetLeft - n.width()) : 0 : n[0] ? -1 * n[0].offsetLeft : 0, e += (s.$list.width() - n.outerWidth()) / 2)), e
        }, e.prototype.getOption = e.prototype.slickGetOption = function(t) {
            return this.options[t]
        }, e.prototype.getNavigableIndexes = function() {
            var t, e = this,
                i = 0,
                n = 0,
                o = [];
            for (!1 === e.options.infinite ? t = e.slideCount : (i = -1 * e.options.slidesToScroll, n = -1 * e.options.slidesToScroll, t = 2 * e.slideCount); i < t;) o.push(i), i = n + e.options.slidesToScroll, n += e.options.slidesToScroll <= e.options.slidesToShow ? e.options.slidesToScroll : e.options.slidesToShow;
            return o
        }, e.prototype.getSlick = function() {
            return this
        }, e.prototype.getSlideCount = function() {
            var e, i, n, o = this;
            return n = !0 === o.options.centerMode ? Math.floor(o.$list.width() / 2) : 0, i = -1 * o.swipeLeft + n, !0 === o.options.swipeToSlide ? (o.$slideTrack.find(".slick-slide").each(function(n, s) {
                var r, a, l;
                if (r = t(s).outerWidth(), a = s.offsetLeft, !0 !== o.options.centerMode && (a += r / 2), l = a + r, i < l) return e = s, !1
            }), Math.abs(t(e).attr("data-slick-index") - o.currentSlide) || 1) : o.options.slidesToScroll
        }, e.prototype.goTo = e.prototype.slickGoTo = function(t, e) {
            this.changeSlide({
                data: {
                    message: "index",
                    index: parseInt(t)
                }
            }, e)
        }, e.prototype.init = function(e) {
            var i = this;
            t(i.$slider).hasClass("slick-initialized") || (t(i.$slider).addClass("slick-initialized"), i.buildRows(), i.buildOut(), i.setProps(), i.startLoad(), i.loadSlider(), i.initializeEvents(), i.updateArrows(), i.updateDots(), i.checkResponsive(!0), i.focusHandler()), e && i.$slider.trigger("init", [i]), !0 === i.options.accessibility && i.initADA(), i.options.autoplay && (i.paused = !1, i.autoPlay())
        }, e.prototype.initADA = function() {
            var e = this,
                i = Math.ceil(e.slideCount / e.options.slidesToShow),
                n = e.getNavigableIndexes().filter(function(t) {
                    return t >= 0 && t < e.slideCount
                });
            e.$slides.add(e.$slideTrack.find(".slick-cloned")).attr({
                "aria-hidden": "true",
                tabindex: "-1"
            }).find("a, input, button, select").attr({
                tabindex: "-1"
            }), null !== e.$dots && (e.$slides.not(e.$slideTrack.find(".slick-cloned")).each(function(i) {
                var o = n.indexOf(i);
                if (t(this).attr({
                        role: "tabpanel",
                        id: "slick-slide" + e.instanceUid + i,
                        tabindex: -1
                    }), -1 !== o) {
                    var s = "slick-slide-control" + e.instanceUid + o;
                    t("#" + s).length && t(this).attr({
                        "aria-describedby": s
                    })
                }
            }), e.$dots.attr("role", "tablist").find("li").each(function(o) {
                var s = n[o];
                t(this).attr({
                    role: "presentation"
                }), t(this).find("button").first().attr({
                    role: "tab",
                    id: "slick-slide-control" + e.instanceUid + o,
                    "aria-controls": "slick-slide" + e.instanceUid + s,
                    "aria-label": o + 1 + " of " + i,
                    "aria-selected": null,
                    tabindex: "-1"
                })
            }).eq(e.currentSlide).find("button").attr({
                "aria-selected": "true",
                tabindex: "0"
            }).end());
            for (var o = e.currentSlide, s = o + e.options.slidesToShow; o < s; o++) e.options.focusOnChange ? e.$slides.eq(o).attr({
                tabindex: "0"
            }) : e.$slides.eq(o).removeAttr("tabindex");
            e.activateADA()
        }, e.prototype.initArrowEvents = function() {
            var t = this;
            !0 === t.options.arrows && t.slideCount > t.options.slidesToShow && (t.$prevArrow.off("click.slick").on("click.slick", {
                message: "previous"
            }, t.changeSlide), t.$nextArrow.off("click.slick").on("click.slick", {
                message: "next"
            }, t.changeSlide), !0 === t.options.accessibility && (t.$prevArrow.on("keydown.slick", t.keyHandler), t.$nextArrow.on("keydown.slick", t.keyHandler)))
        }, e.prototype.initDotEvents = function() {
            var e = this;
            !0 === e.options.dots && e.slideCount > e.options.slidesToShow && (t("li", e.$dots).on("click.slick", {
                message: "index"
            }, e.changeSlide), !0 === e.options.accessibility && e.$dots.on("keydown.slick", e.keyHandler)), !0 === e.options.dots && !0 === e.options.pauseOnDotsHover && e.slideCount > e.options.slidesToShow && t("li", e.$dots).on("mouseenter.slick", t.proxy(e.interrupt, e, !0)).on("mouseleave.slick", t.proxy(e.interrupt, e, !1))
        }, e.prototype.initSlideEvents = function() {
            var e = this;
            e.options.pauseOnHover && (e.$list.on("mouseenter.slick", t.proxy(e.interrupt, e, !0)), e.$list.on("mouseleave.slick", t.proxy(e.interrupt, e, !1)))
        }, e.prototype.initializeEvents = function() {
            var e = this;
            e.initArrowEvents(), e.initDotEvents(), e.initSlideEvents(), e.$list.on("touchstart.slick mousedown.slick", {
                action: "start"
            }, e.swipeHandler), e.$list.on("touchmove.slick mousemove.slick", {
                action: "move"
            }, e.swipeHandler), e.$list.on("touchend.slick mouseup.slick", {
                action: "end"
            }, e.swipeHandler), e.$list.on("touchcancel.slick mouseleave.slick", {
                action: "end"
            }, e.swipeHandler), e.$list.on("click.slick", e.clickHandler), t(document).on(e.visibilityChange, t.proxy(e.visibility, e)), !0 === e.options.accessibility && e.$list.on("keydown.slick", e.keyHandler), !0 === e.options.focusOnSelect && t(e.$slideTrack).children().on("click.slick", e.selectHandler), t(window).on("orientationchange.slick.slick-" + e.instanceUid, t.proxy(e.orientationChange, e)), t(window).on("resize.slick.slick-" + e.instanceUid, t.proxy(e.resize, e)), t("[draggable!=true]", e.$slideTrack).on("dragstart", e.preventDefault), t(window).on("load.slick.slick-" + e.instanceUid, e.setPosition), t(e.setPosition)
        }, e.prototype.initUI = function() {
            var t = this;
            !0 === t.options.arrows && t.slideCount > t.options.slidesToShow && (t.$prevArrow.show(), t.$nextArrow.show()), !0 === t.options.dots && t.slideCount > t.options.slidesToShow && t.$dots.show()
        }, e.prototype.keyHandler = function(t) {
            var e = this;
            t.target.tagName.match("TEXTAREA|INPUT|SELECT") || (37 === t.keyCode && !0 === e.options.accessibility ? e.changeSlide({
                data: {
                    message: !0 === e.options.rtl ? "next" : "previous"
                }
            }) : 39 === t.keyCode && !0 === e.options.accessibility && e.changeSlide({
                data: {
                    message: !0 === e.options.rtl ? "previous" : "next"
                }
            }))
        }, e.prototype.lazyLoad = function() {
            function e(e) {
                t("img[data-lazy]", e).each(function() {
                    var e = t(this),
                        i = t(this).attr("data-lazy"),
                        n = t(this).attr("data-srcset"),
                        o = t(this).attr("data-sizes") || r.$slider.attr("data-sizes"),
                        s = document.createElement("img");
                    s.onload = function() {
                        e.animate({
                            opacity: 0
                        }, 100, function() {
                            n && (e.attr("srcset", n), o && e.attr("sizes", o)), e.attr("src", i).animate({
                                opacity: 1
                            }, 200, function() {
                                e.removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading")
                            }), r.$slider.trigger("lazyLoaded", [r, e, i])
                        })
                    }, s.onerror = function() {
                        e.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"), r.$slider.trigger("lazyLoadError", [r, e, i])
                    }, s.src = i
                })
            }
            var i, n, o, s, r = this;
            if (!0 === r.options.centerMode ? !0 === r.options.infinite ? (o = r.currentSlide + (r.options.slidesToShow / 2 + 1), s = o + r.options.slidesToShow + 2) : (o = Math.max(0, r.currentSlide - (r.options.slidesToShow / 2 + 1)), s = r.options.slidesToShow / 2 + 1 + 2 + r.currentSlide) : (o = r.options.infinite ? r.options.slidesToShow + r.currentSlide : r.currentSlide, s = Math.ceil(o + r.options.slidesToShow), !0 === r.options.fade && (o > 0 && o--, s <= r.slideCount && s++)), i = r.$slider.find(".slick-slide").slice(o, s), "anticipated" === r.options.lazyLoad)
                for (var a = o - 1, l = s, c = r.$slider.find(".slick-slide"), d = 0; d < r.options.slidesToScroll; d++) a < 0 && (a = r.slideCount - 1), i = i.add(c.eq(a)), i = i.add(c.eq(l)), a--, l++;
            e(i), r.slideCount <= r.options.slidesToShow ? (n = r.$slider.find(".slick-slide"), e(n)) : r.currentSlide >= r.slideCount - r.options.slidesToShow ? (n = r.$slider.find(".slick-cloned").slice(0, r.options.slidesToShow), e(n)) : 0 === r.currentSlide && (n = r.$slider.find(".slick-cloned").slice(-1 * r.options.slidesToShow), e(n))
        }, e.prototype.loadSlider = function() {
            var t = this;
            t.setPosition(), t.$slideTrack.css({
                opacity: 1
            }), t.$slider.removeClass("slick-loading"), t.initUI(), "progressive" === t.options.lazyLoad && t.progressiveLazyLoad()
        }, e.prototype.next = e.prototype.slickNext = function() {
            this.changeSlide({
                data: {
                    message: "next"
                }
            })
        }, e.prototype.orientationChange = function() {
            var t = this;
            t.checkResponsive(), t.setPosition()
        }, e.prototype.pause = e.prototype.slickPause = function() {
            var t = this;
            t.autoPlayClear(), t.paused = !0
        }, e.prototype.play = e.prototype.slickPlay = function() {
            var t = this;
            t.autoPlay(), t.options.autoplay = !0, t.paused = !1, t.focussed = !1, t.interrupted = !1
        }, e.prototype.postSlide = function(e) {
            var i = this;
            if (!i.unslicked && (i.$slider.trigger("afterChange", [i, e]), i.animating = !1, i.slideCount > i.options.slidesToShow && i.setPosition(), i.swipeLeft = null, i.options.autoplay && i.autoPlay(), !0 === i.options.accessibility && (i.initADA(), i.options.focusOnChange))) {
                t(i.$slides.get(i.currentSlide)).attr("tabindex", 0).focus()
            }
        }, e.prototype.prev = e.prototype.slickPrev = function() {
            this.changeSlide({
                data: {
                    message: "previous"
                }
            })
        }, e.prototype.preventDefault = function(t) {
            t.preventDefault()
        }, e.prototype.progressiveLazyLoad = function(e) {
            e = e || 1;
            var i, n, o, s, r, a = this,
                l = t("img[data-lazy]", a.$slider);
            l.length ? (i = l.first(), n = i.attr("data-lazy"), o = i.attr("data-srcset"), s = i.attr("data-sizes") || a.$slider.attr("data-sizes"), r = document.createElement("img"), r.onload = function() {
                o && (i.attr("srcset", o), s && i.attr("sizes", s)), i.attr("src", n).removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading"), !0 === a.options.adaptiveHeight && a.setPosition(), a.$slider.trigger("lazyLoaded", [a, i, n]), a.progressiveLazyLoad()
            }, r.onerror = function() {
                e < 3 ? setTimeout(function() {
                    a.progressiveLazyLoad(e + 1)
                }, 500) : (i.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"), a.$slider.trigger("lazyLoadError", [a, i, n]), a.progressiveLazyLoad())
            }, r.src = n) : a.$slider.trigger("allImagesLoaded", [a])
        }, e.prototype.refresh = function(e) {
            var i, n, o = this;
            n = o.slideCount - o.options.slidesToShow, !o.options.infinite && o.currentSlide > n && (o.currentSlide = n), o.slideCount <= o.options.slidesToShow && (o.currentSlide = 0), i = o.currentSlide, o.destroy(!0), t.extend(o, o.initials, {
                currentSlide: i
            }), o.init(), e || o.changeSlide({
                data: {
                    message: "index",
                    index: i
                }
            }, !1)
        }, e.prototype.registerBreakpoints = function() {
            var e, i, n, o = this,
                s = o.options.responsive || null;
            if ("array" === t.type(s) && s.length) {
                o.respondTo = o.options.respondTo || "window";
                for (e in s)
                    if (n = o.breakpoints.length - 1, s.hasOwnProperty(e)) {
                        for (i = s[e].breakpoint; n >= 0;) o.breakpoints[n] && o.breakpoints[n] === i && o.breakpoints.splice(n, 1), n--;
                        o.breakpoints.push(i), o.breakpointSettings[i] = s[e].settings
                    } o.breakpoints.sort(function(t, e) {
                    return o.options.mobileFirst ? t - e : e - t
                })
            }
        }, e.prototype.reinit = function() {
            var e = this;
            e.$slides = e.$slideTrack.children(e.options.slide).addClass("slick-slide"), e.slideCount = e.$slides.length, e.currentSlide >= e.slideCount && 0 !== e.currentSlide && (e.currentSlide = e.currentSlide - e.options.slidesToScroll), e.slideCount <= e.options.slidesToShow && (e.currentSlide = 0), e.registerBreakpoints(), e.setProps(), e.setupInfinite(), e.buildArrows(), e.updateArrows(), e.initArrowEvents(), e.buildDots(), e.updateDots(), e.initDotEvents(), e.cleanUpSlideEvents(), e.initSlideEvents(), e.checkResponsive(!1, !0), !0 === e.options.focusOnSelect && t(e.$slideTrack).children().on("click.slick", e.selectHandler), e.setSlideClasses("number" == typeof e.currentSlide ? e.currentSlide : 0), e.setPosition(), e.focusHandler(), e.paused = !e.options.autoplay, e.autoPlay(), e.$slider.trigger("reInit", [e])
        }, e.prototype.resize = function() {
            var e = this;
            t(window).width() !== e.windowWidth && (clearTimeout(e.windowDelay), e.windowDelay = window.setTimeout(function() {
                e.windowWidth = t(window).width(), e.checkResponsive(), e.unslicked || e.setPosition()
            }, 50))
        }, e.prototype.removeSlide = e.prototype.slickRemove = function(t, e, i) {
            var n = this;
            if ("boolean" == typeof t ? (e = t, t = !0 === e ? 0 : n.slideCount - 1) : t = !0 === e ? --t : t, n.slideCount < 1 || t < 0 || t > n.slideCount - 1) return !1;
            n.unload(), !0 === i ? n.$slideTrack.children().remove() : n.$slideTrack.children(this.options.slide).eq(t).remove(), n.$slides = n.$slideTrack.children(this.options.slide), n.$slideTrack.children(this.options.slide).detach(), n.$slideTrack.append(n.$slides), n.$slidesCache = n.$slides, n.reinit()
        }, e.prototype.setCSS = function(t) {
            var e, i, n = this,
                o = {};
            !0 === n.options.rtl && (t = -t), e = "left" == n.positionProp ? Math.ceil(t) + "px" : "0px", i = "top" == n.positionProp ? Math.ceil(t) + "px" : "0px", o[n.positionProp] = t, !1 === n.transformsEnabled ? n.$slideTrack.css(o) : (o = {}, !1 === n.cssTransitions ? (o[n.animType] = "translate(" + e + ", " + i + ")", n.$slideTrack.css(o)) : (o[n.animType] = "translate3d(" + e + ", " + i + ", 0px)", n.$slideTrack.css(o)))
        }, e.prototype.setDimensions = function() {
            var t = this;
            !1 === t.options.vertical ? !0 === t.options.centerMode && t.$list.css({
                padding: "0px " + t.options.centerPadding
            }) : (t.$list.height(t.$slides.first().outerHeight(!0) * t.options.slidesToShow), !0 === t.options.centerMode && t.$list.css({
                padding: t.options.centerPadding + " 0px"
            })), t.listWidth = t.$list.width(), t.listHeight = t.$list.height(), !1 === t.options.vertical && !1 === t.options.variableWidth ? (t.slideWidth = Math.ceil(t.listWidth / t.options.slidesToShow), t.$slideTrack.width(Math.ceil(t.slideWidth * t.$slideTrack.children(".slick-slide").length))) : !0 === t.options.variableWidth ? t.$slideTrack.width(5e3 * t.slideCount) : (t.slideWidth = Math.ceil(t.listWidth), t.$slideTrack.height(Math.ceil(t.$slides.first().outerHeight(!0) * t.$slideTrack.children(".slick-slide").length)));
            var e = t.$slides.first().outerWidth(!0) - t.$slides.first().width();
            !1 === t.options.variableWidth && t.$slideTrack.children(".slick-slide").width(t.slideWidth - e)
        }, e.prototype.setFade = function() {
            var e, i = this;
            i.$slides.each(function(n, o) {
                e = i.slideWidth * n * -1, !0 === i.options.rtl ? t(o).css({
                    position: "relative",
                    right: e,
                    top: 0,
                    zIndex: i.options.zIndex - 2,
                    opacity: 0
                }) : t(o).css({
                    position: "relative",
                    left: e,
                    top: 0,
                    zIndex: i.options.zIndex - 2,
                    opacity: 0
                })
            }), i.$slides.eq(i.currentSlide).css({
                zIndex: i.options.zIndex - 1,
                opacity: 1
            })
        }, e.prototype.setHeight = function() {
            var t = this;
            if (1 === t.options.slidesToShow && !0 === t.options.adaptiveHeight && !1 === t.options.vertical) {
                var e = t.$slides.eq(t.currentSlide).outerHeight(!0);
                t.$list.css("height", e)
            }
        }, e.prototype.setOption = e.prototype.slickSetOption = function() {
            var e, i, n, o, s, r = this,
                a = !1;
            if ("object" === t.type(arguments[0]) ? (n = arguments[0], a = arguments[1], s = "multiple") : "string" === t.type(arguments[0]) && (n = arguments[0], o = arguments[1], a = arguments[2], "responsive" === arguments[0] && "array" === t.type(arguments[1]) ? s = "responsive" : void 0 !== arguments[1] && (s = "single")), "single" === s) r.options[n] = o;
            else if ("multiple" === s) t.each(n, function(t, e) {
                r.options[t] = e
            });
            else if ("responsive" === s)
                for (i in o)
                    if ("array" !== t.type(r.options.responsive)) r.options.responsive = [o[i]];
                    else {
                        for (e = r.options.responsive.length - 1; e >= 0;) r.options.responsive[e].breakpoint === o[i].breakpoint && r.options.responsive.splice(e, 1), e--;
                        r.options.responsive.push(o[i])
                    } a && (r.unload(), r.reinit())
        }, e.prototype.setPosition = function() {
            var t = this;
            t.setDimensions(), t.setHeight(), !1 === t.options.fade ? t.setCSS(t.getLeft(t.currentSlide)) : t.setFade(), t.$slider.trigger("setPosition", [t])
        }, e.prototype.setProps = function() {
            var t = this,
                e = document.body.style;
            t.positionProp = !0 === t.options.vertical ? "top" : "left", "top" === t.positionProp ? t.$slider.addClass("slick-vertical") : t.$slider.removeClass("slick-vertical"), void 0 === e.WebkitTransition && void 0 === e.MozTransition && void 0 === e.msTransition || !0 === t.options.useCSS && (t.cssTransitions = !0), t.options.fade && ("number" == typeof t.options.zIndex ? t.options.zIndex < 3 && (t.options.zIndex = 3) : t.options.zIndex = t.defaults.zIndex), void 0 !== e.OTransform && (t.animType = "OTransform", t.transformType = "-o-transform", t.transitionType = "OTransition", void 0 === e.perspectiveProperty && void 0 === e.webkitPerspective && (t.animType = !1)), void 0 !== e.MozTransform && (t.animType = "MozTransform", t.transformType = "-moz-transform", t.transitionType = "MozTransition", void 0 === e.perspectiveProperty && void 0 === e.MozPerspective && (t.animType = !1)), void 0 !== e.webkitTransform && (t.animType = "webkitTransform", t.transformType = "-webkit-transform", t.transitionType = "webkitTransition", void 0 === e.perspectiveProperty && void 0 === e.webkitPerspective && (t.animType = !1)), void 0 !== e.msTransform && (t.animType = "msTransform", t.transformType = "-ms-transform", t.transitionType = "msTransition", void 0 === e.msTransform && (t.animType = !1)), void 0 !== e.transform && !1 !== t.animType && (t.animType = "transform", t.transformType = "transform", t.transitionType = "transition"), t.transformsEnabled = t.options.useTransform && null !== t.animType && !1 !== t.animType
        }, e.prototype.setSlideClasses = function(t) {
            var e, i, n, o, s = this;
            if (i = s.$slider.find(".slick-slide").removeClass("slick-active slick-center slick-current").attr("aria-hidden", "true"), s.$slides.eq(t).addClass("slick-current"), !0 === s.options.centerMode) {
                var r = s.options.slidesToShow % 2 == 0 ? 1 : 0;
                e = Math.floor(s.options.slidesToShow / 2), !0 === s.options.infinite && (t >= e && t <= s.slideCount - 1 - e ? s.$slides.slice(t - e + r, t + e + 1).addClass("slick-active").attr("aria-hidden", "false") : (n = s.options.slidesToShow + t, i.slice(n - e + 1 + r, n + e + 2).addClass("slick-active").attr("aria-hidden", "false")), 0 === t ? i.eq(i.length - 1 - s.options.slidesToShow).addClass("slick-center") : t === s.slideCount - 1 && i.eq(s.options.slidesToShow).addClass("slick-center")), s.$slides.eq(t).addClass("slick-center")
            } else t >= 0 && t <= s.slideCount - s.options.slidesToShow ? s.$slides.slice(t, t + s.options.slidesToShow).addClass("slick-active").attr("aria-hidden", "false") : i.length <= s.options.slidesToShow ? i.addClass("slick-active").attr("aria-hidden", "false") : (o = s.slideCount % s.options.slidesToShow, n = !0 === s.options.infinite ? s.options.slidesToShow + t : t, s.options.slidesToShow == s.options.slidesToScroll && s.slideCount - t < s.options.slidesToShow ? i.slice(n - (s.options.slidesToShow - o), n + o).addClass("slick-active").attr("aria-hidden", "false") : i.slice(n, n + s.options.slidesToShow).addClass("slick-active").attr("aria-hidden", "false"));
            "ondemand" !== s.options.lazyLoad && "anticipated" !== s.options.lazyLoad || s.lazyLoad()
        }, e.prototype.setupInfinite = function() {
            var e, i, n, o = this;
            if (!0 === o.options.fade && (o.options.centerMode = !1), !0 === o.options.infinite && !1 === o.options.fade && (i = null, o.slideCount > o.options.slidesToShow)) {
                for (n = !0 === o.options.centerMode ? o.options.slidesToShow + 1 : o.options.slidesToShow, e = o.slideCount; e > o.slideCount - n; e -= 1) i = e - 1, t(o.$slides[i]).clone(!0).attr("id", "").attr("data-slick-index", i - o.slideCount).prependTo(o.$slideTrack).addClass("slick-cloned");
                for (e = 0; e < n + o.slideCount; e += 1) i = e, t(o.$slides[i]).clone(!0).attr("id", "").attr("data-slick-index", i + o.slideCount).appendTo(o.$slideTrack).addClass("slick-cloned");
                o.$slideTrack.find(".slick-cloned").find("[id]").each(function() {
                    t(this).attr("id", "")
                })
            }
        }, e.prototype.interrupt = function(t) {
            var e = this;
            t || e.autoPlay(), e.interrupted = t
        }, e.prototype.selectHandler = function(e) {
            var i = this,
                n = t(e.target).is(".slick-slide") ? t(e.target) : t(e.target).parents(".slick-slide"),
                o = parseInt(n.attr("data-slick-index"));
            if (o || (o = 0), i.slideCount <= i.options.slidesToShow) return void i.slideHandler(o, !1, !0);
            i.slideHandler(o)
        }, e.prototype.slideHandler = function(t, e, i) {
            var n, o, s, r, a, l = null,
                c = this;
            if (e = e || !1, !(!0 === c.animating && !0 === c.options.waitForAnimate || !0 === c.options.fade && c.currentSlide === t)) {
                if (!1 === e && c.asNavFor(t), !1 === c.options.infinite && t < 0 && (c.currentSlide = 0, c.updateArrows()), n = t, l = c.getLeft(n), r = c.getLeft(c.currentSlide), c.currentLeft = null === c.swipeLeft ? r : c.swipeLeft, !1 === c.options.infinite && !1 === c.options.centerMode && (t < 0 || t > c.getDotCount() * c.options.slidesToScroll)) return void(!1 === c.options.fade && (n = c.currentSlide, !0 !== i && c.slideCount > c.options.slidesToShow ? c.animateSlide(r, function() {
                    c.postSlide(n)
                }) : c.postSlide(n)));
                if (!1 === c.options.infinite && !0 === c.options.centerMode && (t < 0 || t > c.slideCount - c.options.slidesToScroll)) return void(!1 === c.options.fade && (n = c.currentSlide, !0 !== i && c.slideCount > c.options.slidesToShow ? c.animateSlide(r, function() {
                    c.postSlide(n)
                }) : c.postSlide(n)));
                if (c.options.autoplay && clearInterval(c.autoPlayTimer), o = n < 0 ? c.slideCount % c.options.slidesToScroll != 0 ? c.slideCount - c.slideCount % c.options.slidesToScroll : c.slideCount + n : n >= c.slideCount ? c.slideCount % c.options.slidesToScroll != 0 ? 0 : n - c.slideCount : n, c.animating = !0, c.$slider.trigger("beforeChange", [c, c.currentSlide, o]), s = c.currentSlide, c.currentSlide = o, c.setSlideClasses(c.currentSlide), c.options.asNavFor && (a = c.getNavTarget(), a = a.slick("getSlick"), a.slideCount <= a.options.slidesToShow && a.setSlideClasses(c.currentSlide)), c.updateDots(), c.updateArrows(), !0 === c.options.fade) return !0 !== i ? (c.fadeSlideOut(s), c.fadeSlide(o, function() {
                    c.postSlide(o)
                })) : c.postSlide(o), void c.animateHeight();
                !0 !== i && c.slideCount > c.options.slidesToShow ? c.animateSlide(l, function() {
                    c.postSlide(o)
                }) : c.postSlide(o)
            }
        }, e.prototype.startLoad = function() {
            var t = this;
            !0 === t.options.arrows && t.slideCount > t.options.slidesToShow && (t.$prevArrow.hide(), t.$nextArrow.hide()), !0 === t.options.dots && t.slideCount > t.options.slidesToShow && t.$dots.hide(), t.$slider.addClass("slick-loading")
        }, e.prototype.swipeDirection = function() {
            var t, e, i, n, o = this;
            return t = o.touchObject.startX - o.touchObject.curX, e = o.touchObject.startY - o.touchObject.curY, i = Math.atan2(e, t), n = Math.round(180 * i / Math.PI), n < 0 && (n = 360 - Math.abs(n)), n <= 45 && n >= 0 ? !1 === o.options.rtl ? "left" : "right" : n <= 360 && n >= 315 ? !1 === o.options.rtl ? "left" : "right" : n >= 135 && n <= 225 ? !1 === o.options.rtl ? "right" : "left" : !0 === o.options.verticalSwiping ? n >= 35 && n <= 135 ? "down" : "up" : "vertical"
        }, e.prototype.swipeEnd = function(t) {
            var e, i, n = this;
            if (n.dragging = !1, n.swiping = !1, n.scrolling) return n.scrolling = !1, !1;
            if (n.interrupted = !1, n.shouldClick = !(n.touchObject.swipeLength > 10), void 0 === n.touchObject.curX) return !1;
            if (!0 === n.touchObject.edgeHit && n.$slider.trigger("edge", [n, n.swipeDirection()]), n.touchObject.swipeLength >= n.touchObject.minSwipe) {
                switch (i = n.swipeDirection()) {
                    case "left":
                    case "down":
                        e = n.options.swipeToSlide ? n.checkNavigable(n.currentSlide + n.getSlideCount()) : n.currentSlide + n.getSlideCount(), n.currentDirection = 0;
                        break;
                    case "right":
                    case "up":
                        e = n.options.swipeToSlide ? n.checkNavigable(n.currentSlide - n.getSlideCount()) : n.currentSlide - n.getSlideCount(), n.currentDirection = 1
                }
                "vertical" != i && (n.slideHandler(e), n.touchObject = {}, n.$slider.trigger("swipe", [n, i]))
            } else n.touchObject.startX !== n.touchObject.curX && (n.slideHandler(n.currentSlide), n.touchObject = {})
        }, e.prototype.swipeHandler = function(t) {
            var e = this;
            if (!(!1 === e.options.swipe || "ontouchend" in document && !1 === e.options.swipe || !1 === e.options.draggable && -1 !== t.type.indexOf("mouse"))) switch (e.touchObject.fingerCount = t.originalEvent && void 0 !== t.originalEvent.touches ? t.originalEvent.touches.length : 1, e.touchObject.minSwipe = e.listWidth / e.options.touchThreshold, !0 === e.options.verticalSwiping && (e.touchObject.minSwipe = e.listHeight / e.options.touchThreshold), t.data.action) {
                case "start":
                    e.swipeStart(t);
                    break;
                case "move":
                    e.swipeMove(t);
                    break;
                case "end":
                    e.swipeEnd(t)
            }
        }, e.prototype.swipeMove = function(t) {
            var e, i, n, o, s, r, a = this;
            return s = void 0 !== t.originalEvent ? t.originalEvent.touches : null, !(!a.dragging || a.scrolling || s && 1 !== s.length) && (e = a.getLeft(a.currentSlide), a.touchObject.curX = void 0 !== s ? s[0].pageX : t.clientX, a.touchObject.curY = void 0 !== s ? s[0].pageY : t.clientY, a.touchObject.swipeLength = Math.round(Math.sqrt(Math.pow(a.touchObject.curX - a.touchObject.startX, 2))), r = Math.round(Math.sqrt(Math.pow(a.touchObject.curY - a.touchObject.startY, 2))), !a.options.verticalSwiping && !a.swiping && r > 4 ? (a.scrolling = !0, !1) : (!0 === a.options.verticalSwiping && (a.touchObject.swipeLength = r), i = a.swipeDirection(), void 0 !== t.originalEvent && a.touchObject.swipeLength > 4 && (a.swiping = !0, t.preventDefault()), o = (!1 === a.options.rtl ? 1 : -1) * (a.touchObject.curX > a.touchObject.startX ? 1 : -1), !0 === a.options.verticalSwiping && (o = a.touchObject.curY > a.touchObject.startY ? 1 : -1), n = a.touchObject.swipeLength, a.touchObject.edgeHit = !1, !1 === a.options.infinite && (0 === a.currentSlide && "right" === i || a.currentSlide >= a.getDotCount() && "left" === i) && (n = a.touchObject.swipeLength * a.options.edgeFriction, a.touchObject.edgeHit = !0), !1 === a.options.vertical ? a.swipeLeft = e + n * o : a.swipeLeft = e + n * (a.$list.height() / a.listWidth) * o, !0 === a.options.verticalSwiping && (a.swipeLeft = e + n * o), !0 !== a.options.fade && !1 !== a.options.touchMove && (!0 === a.animating ? (a.swipeLeft = null, !1) : void a.setCSS(a.swipeLeft))))
        }, e.prototype.swipeStart = function(t) {
            var e, i = this;
            if (i.interrupted = !0, 1 !== i.touchObject.fingerCount || i.slideCount <= i.options.slidesToShow) return i.touchObject = {}, !1;
            void 0 !== t.originalEvent && void 0 !== t.originalEvent.touches && (e = t.originalEvent.touches[0]), i.touchObject.startX = i.touchObject.curX = void 0 !== e ? e.pageX : t.clientX, i.touchObject.startY = i.touchObject.curY = void 0 !== e ? e.pageY : t.clientY, i.dragging = !0
        }, e.prototype.unfilterSlides = e.prototype.slickUnfilter = function() {
            var t = this;
            null !== t.$slidesCache && (t.unload(), t.$slideTrack.children(this.options.slide).detach(), t.$slidesCache.appendTo(t.$slideTrack), t.reinit())
        }, e.prototype.unload = function() {
            var e = this;
            t(".slick-cloned", e.$slider).remove(), e.$dots && e.$dots.remove(), e.$prevArrow && e.htmlExpr.test(e.options.prevArrow) && e.$prevArrow.remove(), e.$nextArrow && e.htmlExpr.test(e.options.nextArrow) && e.$nextArrow.remove(), e.$slides.removeClass("slick-slide slick-active slick-visible slick-current").attr("aria-hidden", "true").css("width", "")
        }, e.prototype.unslick = function(t) {
            var e = this;
            e.$slider.trigger("unslick", [e, t]), e.destroy()
        }, e.prototype.updateArrows = function() {
            var t = this;
            Math.floor(t.options.slidesToShow / 2), !0 === t.options.arrows && t.slideCount > t.options.slidesToShow && !t.options.infinite && (t.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false"), t.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false"), 0 === t.currentSlide ? (t.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true"), t.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false")) : t.currentSlide >= t.slideCount - t.options.slidesToShow && !1 === t.options.centerMode ? (t.$nextArrow.addClass("slick-disabled").attr("aria-disabled", "true"), t.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false")) : t.currentSlide >= t.slideCount - 1 && !0 === t.options.centerMode && (t.$nextArrow.addClass("slick-disabled").attr("aria-disabled", "true"), t.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false")))
        }, e.prototype.updateDots = function() {
            var t = this;
            null !== t.$dots && (t.$dots.find("li").removeClass("slick-active").end(), t.$dots.find("li").eq(Math.floor(t.currentSlide / t.options.slidesToScroll)).addClass("slick-active"))
        }, e.prototype.visibility = function() {
            var t = this;
            t.options.autoplay && (document[t.hidden] ? t.interrupted = !0 : t.interrupted = !1)
        }, t.fn.slick = function() {
            var t, i, n = this,
                o = arguments[0],
                s = Array.prototype.slice.call(arguments, 1),
                r = n.length;
            for (t = 0; t < r; t++)
                if ("object" == typeof o || void 0 === o ? n[t].slick = new e(n[t], o) : i = n[t].slick[o].apply(n[t].slick, s), void 0 !== i) return i;
            return n
        }
    })
}, function(t, e, i) {
    "use strict";
    t.exports = function(t, e, i, n) {
        function o() {
            function o() {
                r = Number(new Date), i.apply(l, d)
            }

            function a() {
                s = void 0
            }
            var l = this,
                c = Number(new Date) - r,
                d = arguments;
            n && !s && o(), s && clearTimeout(s), void 0 === n && c > t ? o() : !0 !== e && (s = setTimeout(n ? a : o, void 0 === n ? t - c : t))
        }
        var s, r = 0;
        return "boolean" != typeof e && (n = i, i = e, e = void 0), o
    }
}, function(t, e, i) {
    var n, o, s = Object.assign || function(t) {
            for (var e = 1; e < arguments.length; e++) {
                var i = arguments[e];
                for (var n in i) Object.prototype.hasOwnProperty.call(i, n) && (t[n] = i[n])
            }
            return t
        },
        r = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
            return typeof t
        } : function(t) {
            return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
        };
    ! function(s, a) {
        "object" === r(e) && void 0 !== t ? t.exports = a() : (n = a, void 0 !== (o = "function" == typeof n ? n.call(e, i, e, t) : n) && (t.exports = o))
    }(0, function() {
        "use strict";

        function t(t, e, i) {
            var n = e._settings;
            !i && a(t) || (C(n.callback_enter, t), N.indexOf(t.tagName) > -1 && (D(t, e), T(t, n.class_loading)), x(t, e), r(t), C(n.callback_set, t))
        }
        var e = {
                elements_selector: "img",
                container: document,
                threshold: 300,
                thresholds: null,
                data_src: "src",
                data_srcset: "srcset",
                data_sizes: "sizes",
                data_bg: "bg",
                class_loading: "loading",
                class_loaded: "loaded",
                class_error: "error",
                load_delay: 0,
                callback_load: null,
                callback_error: null,
                callback_set: null,
                callback_enter: null,
                callback_finish: null,
                to_webp: !1
            },
            i = function(t) {
                return s({}, e, t)
            },
            n = function(t, e) {
                return t.getAttribute("data-" + e)
            },
            o = function(t, e, i) {
                var n = "data-" + e;
                null !== i ? t.setAttribute(n, i) : t.removeAttribute(n)
            },
            r = function(t) {
                return o(t, "was-processed", "true")
            },
            a = function(t) {
                return "true" === n(t, "was-processed")
            },
            l = function(t, e) {
                return o(t, "ll-timeout", e)
            },
            c = function(t) {
                return n(t, "ll-timeout")
            },
            d = function(t) {
                return t.filter(function(t) {
                    return !a(t)
                })
            },
            u = function(t, e) {
                return t.filter(function(t) {
                    return t !== e
                })
            },
            f = function(t, e) {
                var i, n = new t(e);
                try {
                    i = new CustomEvent("LazyLoad::Initialized", {
                        detail: {
                            instance: n
                        }
                    })
                } catch (t) {
                    (i = document.createEvent("CustomEvent")).initCustomEvent("LazyLoad::Initialized", !1, !1, {
                        instance: n
                    })
                }
                window.dispatchEvent(i)
            },
            p = function(t, e) {
                return e ? t.replace(/\.(jpe?g|png)/gi, ".webp") : t
            },
            h = "undefined" != typeof window,
            m = h && !("onscroll" in window) || /(gle|ing|ro)bot|crawl|spider/i.test(navigator.userAgent),
            g = h && "IntersectionObserver" in window,
            v = h && "classList" in document.createElement("p"),
            y = h && function() {
                var t = document.createElement("canvas");
                return !(!t.getContext || !t.getContext("2d")) && 0 === t.toDataURL("image/webp").indexOf("data:image/webp")
            }(),
            w = function(t, e, i, o) {
                for (var s, r = 0; s = t.children[r]; r += 1)
                    if ("SOURCE" === s.tagName) {
                        var a = n(s, i);
                        _(s, e, a, o)
                    }
            },
            _ = function(t, e, i, n) {
                i && t.setAttribute(e, p(i, n))
            },
            b = function(t, e) {
                var i = y && e.to_webp,
                    o = n(t, e.data_src),
                    s = n(t, e.data_bg);
                if (o) {
                    var r = p(o, i);
                    t.style.backgroundImage = 'url("' + r + '")'
                }
                if (s) {
                    var a = p(s, i);
                    t.style.backgroundImage = a
                }
            },
            S = {
                IMG: function(t, e) {
                    var i = y && e.to_webp,
                        o = e.data_srcset,
                        s = t.parentNode;
                    s && "PICTURE" === s.tagName && w(s, "srcset", o, i);
                    var r = n(t, e.data_sizes);
                    _(t, "sizes", r);
                    var a = n(t, o);
                    _(t, "srcset", a, i);
                    var l = n(t, e.data_src);
                    _(t, "src", l, i)
                },
                IFRAME: function(t, e) {
                    var i = n(t, e.data_src);
                    _(t, "src", i)
                },
                VIDEO: function(t, e) {
                    var i = e.data_src,
                        o = n(t, i);
                    w(t, "src", i), _(t, "src", o), t.load()
                }
            },
            x = function(t, e) {
                var i = e._settings,
                    n = t.tagName,
                    o = S[n];
                if (o) return o(t, i), e._updateLoadingCount(1), void(e._elements = u(e._elements, t));
                b(t, i)
            },
            T = function(t, e) {
                v ? t.classList.add(e) : t.className += (t.className ? " " : "") + e
            },
            k = function(t, e) {
                v ? t.classList.remove(e) : t.className = t.className.replace(new RegExp("(^|\\s+)" + e + "(\\s+|$)"), " ").replace(/^\s+/, "").replace(/\s+$/, "")
            },
            C = function(t, e) {
                t && t(e)
            },
            E = function(t, e, i) {
                t.addEventListener(e, i)
            },
            A = function(t, e, i) {
                t.removeEventListener(e, i)
            },
            O = function(t, e, i) {
                E(t, "load", e), E(t, "loadeddata", e), E(t, "error", i)
            },
            I = function(t, e, i) {
                A(t, "load", e), A(t, "loadeddata", e), A(t, "error", i)
            },
            $ = function(t, e, i) {
                var n = i._settings,
                    o = e ? n.class_loaded : n.class_error,
                    s = e ? n.callback_load : n.callback_error,
                    r = t.target;
                k(r, n.class_loading), T(r, o), C(s, r), i._updateLoadingCount(-1)
            },
            D = function(t, e) {
                var i = function i(o) {
                        $(o, !0, e), I(t, i, n)
                    },
                    n = function n(o) {
                        $(o, !1, e), I(t, i, n)
                    };
                O(t, i, n)
            },
            N = ["IMG", "IFRAME", "VIDEO"],
            L = function(e, i, n) {
                t(e, n), i.unobserve(e)
            },
            P = function(t) {
                var e = c(t);
                e && (clearTimeout(e), l(t, null))
            },
            j = function(t, e, i) {
                var n = i._settings.load_delay,
                    o = c(t);
                o || (o = setTimeout(function() {
                    L(t, e, i), P(t)
                }, n), l(t, o))
            },
            H = function(t) {
                return t.isIntersecting || t.intersectionRatio > 0
            },
            B = function(t) {
                return {
                    root: t.container === document ? null : t.container,
                    rootMargin: t.thresholds || t.threshold + "px"
                }
            },
            M = function(t, e) {
                this._settings = i(t), this._setObserver(), this._loadingCount = 0, this.update(e)
            };
        return M.prototype = {
            _manageIntersection: function(t) {
                var e = this._observer,
                    i = this._settings.load_delay,
                    n = t.target;
                i ? H(t) ? j(n, e, this) : P(n) : H(t) && L(n, e, this)
            },
            _onIntersection: function(t) {
                t.forEach(this._manageIntersection.bind(this))
            },
            _setObserver: function() {
                g && (this._observer = new IntersectionObserver(this._onIntersection.bind(this), B(this._settings)))
            },
            _updateLoadingCount: function(t) {
                this._loadingCount += t, 0 === this._elements.length && 0 === this._loadingCount && C(this._settings.callback_finish)
            },
            update: function(t) {
                var e = this,
                    i = this._settings,
                    n = t || i.container.querySelectorAll(i.elements_selector);
                this._elements = d(Array.prototype.slice.call(n)), !m && this._observer ? this._elements.forEach(function(t) {
                    e._observer.observe(t)
                }) : this.loadAll()
            },
            destroy: function() {
                var t = this;
                this._observer && (this._elements.forEach(function(e) {
                    t._observer.unobserve(e)
                }), this._observer = null), this._elements = null, this._settings = null
            },
            load: function(e, i) {
                t(e, this, i)
            },
            loadAll: function() {
                var t = this;
                this._elements.forEach(function(e) {
                    t.load(e)
                })
            }
        }, h && function(t, e) {
            if (e)
                if (e.length)
                    for (var i, n = 0; i = e[n]; n += 1) f(t, i);
                else f(t, e)
        }(M, window.lazyLoadOptions), M
    })
}, function(t, e, i) {
    "use strict";
    ! function() {
        function t(n) {
            if (!n) throw new Error("No options passed to Waypoint constructor");
            if (!n.element) throw new Error("No element option passed to Waypoint constructor");
            if (!n.handler) throw new Error("No handler option passed to Waypoint constructor");
            this.key = "waypoint-" + e, this.options = t.Adapter.extend({}, t.defaults, n), this.element = this.options.element, this.adapter = new t.Adapter(this.element), this.callback = n.handler, this.axis = this.options.horizontal ? "horizontal" : "vertical", this.enabled = this.options.enabled, this.triggerPoint = null, this.group = t.Group.findOrCreate({
                name: this.options.group,
                axis: this.axis
            }), this.context = t.Context.findOrCreateByElement(this.options.context), t.offsetAliases[this.options.offset] && (this.options.offset = t.offsetAliases[this.options.offset]), this.group.add(this), this.context.add(this), i[this.key] = this, e += 1
        }
        var e = 0,
            i = {};
        t.prototype.queueTrigger = function(t) {
            this.group.queueTrigger(this, t)
        }, t.prototype.trigger = function(t) {
            this.enabled && this.callback && this.callback.apply(this, t)
        }, t.prototype.destroy = function() {
            this.context.remove(this), this.group.remove(this), delete i[this.key]
        }, t.prototype.disable = function() {
            return this.enabled = !1, this
        }, t.prototype.enable = function() {
            return this.context.refresh(), this.enabled = !0, this
        }, t.prototype.next = function() {
            return this.group.next(this)
        }, t.prototype.previous = function() {
            return this.group.previous(this)
        }, t.invokeAll = function(t) {
            var e = [];
            for (var n in i) e.push(i[n]);
            for (var o = 0, s = e.length; s > o; o++) e[o][t]()
        }, t.destroyAll = function() {
            t.invokeAll("destroy")
        }, t.disableAll = function() {
            t.invokeAll("disable")
        }, t.enableAll = function() {
            t.Context.refreshAll();
            for (var e in i) i[e].enabled = !0;
            return this
        }, t.refreshAll = function() {
            t.Context.refreshAll()
        }, t.viewportHeight = function() {
            return window.innerHeight || document.documentElement.clientHeight
        }, t.viewportWidth = function() {
            return document.documentElement.clientWidth
        }, t.adapters = [], t.defaults = {
            context: window,
            continuous: !0,
            enabled: !0,
            group: "default",
            horizontal: !1,
            offset: 0
        }, t.offsetAliases = {
            "bottom-in-view": function() {
                return this.context.innerHeight() - this.adapter.outerHeight()
            },
            "right-in-view": function() {
                return this.context.innerWidth() - this.adapter.outerWidth()
            }
        }, window.Waypoint = t
    }(),
    function() {
        function t(t) {
            window.setTimeout(t, 1e3 / 60)
        }

        function e(t) {
            this.element = t, this.Adapter = o.Adapter, this.adapter = new this.Adapter(t), this.key = "waypoint-context-" + i, this.didScroll = !1, this.didResize = !1, this.oldScroll = {
                x: this.adapter.scrollLeft(),
                y: this.adapter.scrollTop()
            }, this.waypoints = {
                vertical: {},
                horizontal: {}
            }, t.waypointContextKey = this.key, n[t.waypointContextKey] = this, i += 1, o.windowContext || (o.windowContext = !0, o.windowContext = new e(window)), this.createThrottledScrollHandler(), this.createThrottledResizeHandler()
        }
        var i = 0,
            n = {},
            o = window.Waypoint,
            s = window.onload;
        e.prototype.add = function(t) {
            var e = t.options.horizontal ? "horizontal" : "vertical";
            this.waypoints[e][t.key] = t, this.refresh()
        }, e.prototype.checkEmpty = function() {
            var t = this.Adapter.isEmptyObject(this.waypoints.horizontal),
                e = this.Adapter.isEmptyObject(this.waypoints.vertical),
                i = this.element == this.element.window;
            t && e && !i && (this.adapter.off(".waypoints"), delete n[this.key])
        }, e.prototype.createThrottledResizeHandler = function() {
            function t() {
                e.handleResize(), e.didResize = !1
            }
            var e = this;
            this.adapter.on("resize.waypoints", function() {
                e.didResize || (e.didResize = !0, o.requestAnimationFrame(t))
            })
        }, e.prototype.createThrottledScrollHandler = function() {
            function t() {
                e.handleScroll(), e.didScroll = !1
            }
            var e = this;
            this.adapter.on("scroll.waypoints", function() {
                (!e.didScroll || o.isTouch) && (e.didScroll = !0, o.requestAnimationFrame(t))
            })
        }, e.prototype.handleResize = function() {
            o.Context.refreshAll()
        }, e.prototype.handleScroll = function() {
            var t = {},
                e = {
                    horizontal: {
                        newScroll: this.adapter.scrollLeft(),
                        oldScroll: this.oldScroll.x,
                        forward: "right",
                        backward: "left"
                    },
                    vertical: {
                        newScroll: this.adapter.scrollTop(),
                        oldScroll: this.oldScroll.y,
                        forward: "down",
                        backward: "up"
                    }
                };
            for (var i in e) {
                var n = e[i],
                    o = n.newScroll > n.oldScroll,
                    s = o ? n.forward : n.backward;
                for (var r in this.waypoints[i]) {
                    var a = this.waypoints[i][r];
                    if (null !== a.triggerPoint) {
                        var l = n.oldScroll < a.triggerPoint,
                            c = n.newScroll >= a.triggerPoint,
                            d = l && c,
                            u = !l && !c;
                        (d || u) && (a.queueTrigger(s), t[a.group.id] = a.group)
                    }
                }
            }
            for (var f in t) t[f].flushTriggers();
            this.oldScroll = {
                x: e.horizontal.newScroll,
                y: e.vertical.newScroll
            }
        }, e.prototype.innerHeight = function() {
            return this.element == this.element.window ? o.viewportHeight() : this.adapter.innerHeight()
        }, e.prototype.remove = function(t) {
            delete this.waypoints[t.axis][t.key], this.checkEmpty()
        }, e.prototype.innerWidth = function() {
            return this.element == this.element.window ? o.viewportWidth() : this.adapter.innerWidth()
        }, e.prototype.destroy = function() {
            var t = [];
            for (var e in this.waypoints)
                for (var i in this.waypoints[e]) t.push(this.waypoints[e][i]);
            for (var n = 0, o = t.length; o > n; n++) t[n].destroy()
        }, e.prototype.refresh = function() {
            var t, e = this.element == this.element.window,
                i = e ? void 0 : this.adapter.offset(),
                n = {};
            this.handleScroll(), t = {
                horizontal: {
                    contextOffset: e ? 0 : i.left,
                    contextScroll: e ? 0 : this.oldScroll.x,
                    contextDimension: this.innerWidth(),
                    oldScroll: this.oldScroll.x,
                    forward: "right",
                    backward: "left",
                    offsetProp: "left"
                },
                vertical: {
                    contextOffset: e ? 0 : i.top,
                    contextScroll: e ? 0 : this.oldScroll.y,
                    contextDimension: this.innerHeight(),
                    oldScroll: this.oldScroll.y,
                    forward: "down",
                    backward: "up",
                    offsetProp: "top"
                }
            };
            for (var s in t) {
                var r = t[s];
                for (var a in this.waypoints[s]) {
                    var l, c, d, u, f, p = this.waypoints[s][a],
                        h = p.options.offset,
                        m = p.triggerPoint,
                        g = 0,
                        v = null == m;
                    p.element !== p.element.window && (g = p.adapter.offset()[r.offsetProp]), "function" == typeof h ? h = h.apply(p) : "string" == typeof h && (h = parseFloat(h), p.options.offset.indexOf("%") > -1 && (h = Math.ceil(r.contextDimension * h / 100))), l = r.contextScroll - r.contextOffset, p.triggerPoint = Math.floor(g + l - h), c = m < r.oldScroll, d = p.triggerPoint >= r.oldScroll, u = c && d, f = !c && !d, !v && u ? (p.queueTrigger(r.backward), n[p.group.id] = p.group) : !v && f ? (p.queueTrigger(r.forward), n[p.group.id] = p.group) : v && r.oldScroll >= p.triggerPoint && (p.queueTrigger(r.forward), n[p.group.id] = p.group)
                }
            }
            return o.requestAnimationFrame(function() {
                for (var t in n) n[t].flushTriggers()
            }), this
        }, e.findOrCreateByElement = function(t) {
            return e.findByElement(t) || new e(t)
        }, e.refreshAll = function() {
            for (var t in n) n[t].refresh()
        }, e.findByElement = function(t) {
            return n[t.waypointContextKey]
        }, window.onload = function() {
            s && s(), e.refreshAll()
        }, o.requestAnimationFrame = function(e) {
            (window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || t).call(window, e)
        }, o.Context = e
    }(),
    function() {
        function t(t, e) {
            return t.triggerPoint - e.triggerPoint
        }

        function e(t, e) {
            return e.triggerPoint - t.triggerPoint
        }

        function i(t) {
            this.name = t.name, this.axis = t.axis, this.id = this.name + "-" + this.axis, this.waypoints = [], this.clearTriggerQueues(), n[this.axis][this.name] = this
        }
        var n = {
                vertical: {},
                horizontal: {}
            },
            o = window.Waypoint;
        i.prototype.add = function(t) {
            this.waypoints.push(t)
        }, i.prototype.clearTriggerQueues = function() {
            this.triggerQueues = {
                up: [],
                down: [],
                left: [],
                right: []
            }
        }, i.prototype.flushTriggers = function() {
            for (var i in this.triggerQueues) {
                var n = this.triggerQueues[i],
                    o = "up" === i || "left" === i;
                n.sort(o ? e : t);
                for (var s = 0, r = n.length; r > s; s += 1) {
                    var a = n[s];
                    (a.options.continuous || s === n.length - 1) && a.trigger([i])
                }
            }
            this.clearTriggerQueues()
        }, i.prototype.next = function(e) {
            this.waypoints.sort(t);
            var i = o.Adapter.inArray(e, this.waypoints);
            return i === this.waypoints.length - 1 ? null : this.waypoints[i + 1]
        }, i.prototype.previous = function(e) {
            this.waypoints.sort(t);
            var i = o.Adapter.inArray(e, this.waypoints);
            return i ? this.waypoints[i - 1] : null
        }, i.prototype.queueTrigger = function(t, e) {
            this.triggerQueues[e].push(t)
        }, i.prototype.remove = function(t) {
            var e = o.Adapter.inArray(t, this.waypoints);
            e > -1 && this.waypoints.splice(e, 1)
        }, i.prototype.first = function() {
            return this.waypoints[0]
        }, i.prototype.last = function() {
            return this.waypoints[this.waypoints.length - 1]
        }, i.findOrCreate = function(t) {
            return n[t.axis][t.name] || new i(t)
        }, o.Group = i
    }(),
    function() {
        function t(t) {
            this.$element = e(t)
        }
        var e = window.jQuery,
            i = window.Waypoint;
        e.each(["innerHeight", "innerWidth", "off", "offset", "on", "outerHeight", "outerWidth", "scrollLeft", "scrollTop"], function(e, i) {
            t.prototype[i] = function() {
                var t = Array.prototype.slice.call(arguments);
                return this.$element[i].apply(this.$element, t)
            }
        }), e.each(["extend", "inArray", "isEmptyObject"], function(i, n) {
            t[n] = e[n]
        }), i.adapters.push({
            name: "jquery",
            Adapter: t
        }), i.Adapter = t
    }(),
    function() {
        function t(t) {
            return function() {
                var i = [],
                    n = arguments[0];
                return t.isFunction(arguments[0]) && (n = t.extend({}, arguments[1]), n.handler = arguments[0]), this.each(function() {
                    var o = t.extend({}, n, {
                        element: this
                    });
                    "string" == typeof o.context && (o.context = t(this).closest(o.context)[0]), i.push(new e(o))
                }), i
            }
        }
        var e = window.Waypoint;
        window.jQuery && (window.jQuery.fn.waypoint = t(window.jQuery)), window.Zepto && (window.Zepto.fn.waypoint = t(window.Zepto))
    }()
}, function(t, e, i) {
    "use strict";
    ! function() {
        function t(n) {
            this.options = e.extend({}, i.defaults, t.defaults, n), this.element = this.options.element, this.$element = e(this.element), this.createWrapper(), this.createWaypoint()
        }
        var e = window.jQuery,
            i = window.Waypoint;
        t.prototype.createWaypoint = function() {
            var t = this.options.handler;
            this.waypoint = new i(e.extend({}, this.options, {
                element: this.wrapper,
                handler: e.proxy(function(e) {
                    var i = this.options.direction.indexOf(e) > -1,
                        n = i ? this.$element.outerHeight(!0) : "";
                    this.$wrapper.height(n), this.$element.toggleClass(this.options.stuckClass, i), t && t.call(this, e)
                }, this)
            }))
        }, t.prototype.createWrapper = function() {
            this.options.wrapper && this.$element.wrap(this.options.wrapper), this.$wrapper = this.$element.parent(), this.wrapper = this.$wrapper[0]
        }, t.prototype.destroy = function() {
            this.$element.parent()[0] === this.wrapper && (this.waypoint.destroy(), this.$element.removeClass(this.options.stuckClass), this.options.wrapper && this.$element.unwrap())
        }, t.defaults = {
            wrapper: '<div class="sticky-wrapper" />',
            stuckClass: "stuck",
            direction: "down right"
        }, i.Sticky = t
    }()
}, function(t, e, i) {
    "use strict";
    var n;
    n = function() {
        return this
    }();
    try {
        n = n || Function("return this")() || (0, eval)("this")
    } catch (t) {
        "object" == typeof window && (n = window)
    }
    t.exports = n
}, function(t, e, i) {
    i(4), t.exports = i(5)
}]);