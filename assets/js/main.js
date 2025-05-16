// Tối ưu hóa main.js
document.addEventListener("DOMContentLoaded", () => {
    // Banner slider
    initSliders()

    // Xử lý nút thêm vào giỏ hàng AJAX
    setupAddToCartButtons()

    // Thông báo thêm vào giỏ hàng
    function showNotification(message) {
        // Kiểm tra xem đã có thông báo chưa
        let notification = document.querySelector(".notification")

        // Nếu chưa có, tạo mới
        if (!notification) {
            notification = document.createElement("div")
            notification.className = "notification"
            document.body.appendChild(notification)
        }

        // Cập nhật nội dung và hiển thị
        notification.textContent = message
        notification.classList.add("show")

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            notification.classList.remove("show")
        }, 3000)
    }

    // Thiết lập các nút thêm vào giỏ hàng
    function setupAddToCartButtons() {
        const addToCartButtons = document.querySelectorAll(".them-vao-gio")

        addToCartButtons.forEach((button) => {
            button.addEventListener("click", function (e) {
                // Ngăn chặn hành vi mặc định của liên kết
                e.preventDefault()

                // Lấy URL từ thuộc tính href của nút
                const url = this.getAttribute("href")

                // Kiểm tra nếu URL chứa "them-vao-gio-ajax.php" thì sử dụng AJAX
                if (url.includes("them-vao-gio-ajax.php")) {
                    // Lấy ID sản phẩm từ URL
                    const productId = url.split("id=")[1]

                    // Gửi yêu cầu AJAX
                    const xhr = new XMLHttpRequest()
                    xhr.open("POST", "xu-ly/them-vao-gio-ajax.php", true)
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")

                    xhr.onload = () => {
                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText)

                                if (response.success) {
                                    // Cập nhật số lượng sản phẩm trong giỏ hàng
                                    const cartCount = document.querySelector(".gio-hang .so-luong")
                                    if (cartCount) {
                                        cartCount.textContent = response.cart_count
                                    }

                                    // Hiển thị thông báo
                                    showNotification("Sản phẩm đã được thêm vào giỏ hàng!")
                                } else {
                                    showNotification(response.message || "Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!")
                                }
                            } catch (e) {
                                console.error("Error parsing JSON:", e)
                                console.log("Response text:", xhr.responseText)

                                // Sử dụng phương thức thông thường khi có lỗi AJAX
                                window.location.href = url
                            }
                        } else {
                            // Sử dụng phương thức thông thường khi có lỗi AJAX
                            window.location.href = url
                        }
                    }

                    xhr.onerror = () => {
                        // Sử dụng phương thức thông thường khi có lỗi AJAX
                        window.location.href = url
                    }

                    xhr.send("san_pham_id=" + productId)
                } else {
                    // Nếu không phải URL AJAX, sử dụng phương thức thông thường
                    window.location.href = url
                }
            })
        })
    }

    // Xử lý tự động ẩn các thông báo thành công
    const successMessages = document.querySelectorAll(".thong-bao-thanh-cong")
    if (successMessages.length > 0) {
        successMessages.forEach((message) => {
            // Thêm transition để có hiệu ứng mờ dần và di chuyển lên
            message.style.transition = "opacity 0.3s, transform 0.3s"

            // Tự động ẩn thông báo sau 3 giây
            setTimeout(() => {
                message.style.opacity = "0"
                message.style.transform = "translateY(-20px)"

                // Xóa phần tử khỏi DOM sau khi hoàn thành hiệu ứng
                setTimeout(() => {
                    message.remove()
                }, 300)
            }, 3000)
        })
    }
})

// Hàm khởi tạo slider - Viết lại hoàn toàn
function initSliders() {
    const sliders = document.querySelectorAll(".slider")

    sliders.forEach((slider) => {
        // Tìm container chứa các slide
        const container = slider.querySelector(".slider-track") || slider

        // Tìm tất cả các slide
        const slides = container.querySelectorAll(".slide")

        if (slides.length === 0) return

        // Thiết lập CSS cho slider và slides
        setupSliderCSS(slider, container, slides)

        // Tìm các nút điều hướng và chỉ báo
        const prevBtn = slider.querySelector(".prev-slide, .slider-prev")
        const nextBtn = slider.querySelector(".next-slide, .slider-next")
        const dots = slider.querySelectorAll(".slider-dot")

        let currentIndex = 0
        let autoSlideTimer = null
        const autoSlideInterval = 5000 // 5 giây

        // Hiển thị slide đầu tiên
        updateSlider(0, false)

        // Bắt đầu tự động chuyển slide
        startAutoSlide()

        // Cập nhật vị trí slider
        function updateSlider(newIndex, animate = true) {
            if (newIndex < 0) {
                newIndex = slides.length - 1
            } else if (newIndex >= slides.length) {
                newIndex = 0
            }

            // Cập nhật chỉ số hiện tại
            currentIndex = newIndex

            // Cập nhật active dot
            if (dots.length > 0) {
                dots.forEach((dot, index) => {
                    dot.classList.toggle("active", index === currentIndex)
                })
            }

            // Hiển thị slide hiện tại
            slides.forEach((slide, index) => {
                if (animate) {
                    slide.style.transition = "opacity 0.5s ease-in-out"
                } else {
                    slide.style.transition = "none"
                }

                // Đặt z-index và opacity
                if (index === currentIndex) {
                    slide.style.opacity = "1"
                    slide.style.zIndex = "2"
                    slide.style.display = "block"
                } else {
                    slide.style.opacity = "0"
                    slide.style.zIndex = "1"
                    // KHÔNG ẩn slide bằng display: none
                }
            })
        }

        // Thiết lập CSS cho slider
        function setupSliderCSS(slider, container, slides) {
            // Đảm bảo slider có position relative
            if (getComputedStyle(slider).position === "static") {
                slider.style.position = "relative"
            }

            // Đảm bảo container có position relative
            container.style.position = "relative"
            container.style.overflow = "hidden"
            container.style.width = "100%"
            container.style.height = "100%"

            // Thiết lập CSS cho từng slide
            slides.forEach((slide, index) => {
                // Đảm bảo slide có position absolute
                slide.style.position = "absolute"
                slide.style.top = "0"
                slide.style.left = "0"
                slide.style.width = "100%"
                slide.style.height = "100%"
                slide.style.opacity = index === 0 ? "1" : "0"
                slide.style.zIndex = index === 0 ? "2" : "1"

                // Đảm bảo tất cả các hình ảnh trong slide đều hiển thị
                const slideImages = slide.querySelectorAll("img")
                slideImages.forEach((img) => {
                    img.style.display = "block"
                    img.style.width = "100%"
                    img.style.height = "auto"
                })
            })
        }

        // Bắt đầu tự động chuyển slide
        function startAutoSlide() {
            // Xóa bộ hẹn giờ hiện tại nếu có
            if (autoSlideTimer) {
                clearInterval(autoSlideTimer)
            }

            // Tạo bộ hẹn giờ mới
            autoSlideTimer = setInterval(() => {
                updateSlider(currentIndex + 1)
            }, autoSlideInterval)
        }

        // Dừng tự động chuyển slide
        function stopAutoSlide() {
            if (autoSlideTimer) {
                clearInterval(autoSlideTimer)
                autoSlideTimer = null
            }
        }

        // Xử lý sự kiện cho nút prev
        if (prevBtn) {
            prevBtn.addEventListener("click", (e) => {
                e.preventDefault()
                updateSlider(currentIndex - 1)
                startAutoSlide() // Khởi động lại bộ hẹn giờ
            })
        }

        // Xử lý sự kiện cho nút next
        if (nextBtn) {
            nextBtn.addEventListener("click", (e) => {
                e.preventDefault()
                updateSlider(currentIndex + 1)
                startAutoSlide() // Khởi động lại bộ hẹn giờ
            })
        }

        // Xử lý sự kiện cho dots
        if (dots.length > 0) {
            dots.forEach((dot, index) => {
                dot.addEventListener("click", (e) => {
                    e.preventDefault()
                    updateSlider(index)
                    startAutoSlide() // Khởi động lại bộ hẹn giờ
                })
            })
        }

        // Dừng tự động chuyển khi hover vào slider
        slider.addEventListener("mouseenter", stopAutoSlide)
        slider.addEventListener("mouseleave", startAutoSlide)

        // Xử lý vuốt trên thiết bị di động
        let touchStartX = 0
        let touchEndX = 0

        slider.addEventListener(
            "touchstart",
            (e) => {
                touchStartX = e.changedTouches[0].screenX
                stopAutoSlide()
            },
            { passive: true },
        )

        slider.addEventListener(
            "touchend",
            (e) => {
                touchEndX = e.changedTouches[0].screenX

                // Vuốt sang phải (prev)
                if (touchEndX > touchStartX + 50) {
                    updateSlider(currentIndex - 1)
                }
                // Vuốt sang trái (next)
                else if (touchEndX < touchStartX - 50) {
                    updateSlider(currentIndex + 1)
                }

                startAutoSlide()
            },
            { passive: true },
        )
    })
}

