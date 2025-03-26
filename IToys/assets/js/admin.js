document.addEventListener("DOMContentLoaded", () => {
    // Toggle user menu
    const userInfo = document.querySelector(".admin-user-info")
    const userMenu = document.querySelector(".admin-user-menu")

    if (userInfo && userMenu) {
        userInfo.addEventListener("click", (e) => {
            e.stopPropagation()
            userMenu.classList.toggle("active")
        })

        document.addEventListener("click", (e) => {
            if (!userMenu.contains(e.target) && !userInfo.contains(e.target)) {
                userMenu.classList.remove("active")
            }
        })
    }

    // Mobile menu toggle
    const mobileMenuToggle = document.createElement("button")
    mobileMenuToggle.className = "mobile-menu-toggle"
    mobileMenuToggle.innerHTML = '<i class="fas fa-bars"></i>'

    const adminHeader = document.querySelector(".admin-header-content")
    if (adminHeader) {
        adminHeader.prepend(mobileMenuToggle)
    }

    const sidebar = document.querySelector(".sidebar")

    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener("click", () => {
            sidebar.classList.toggle("active")
        })

        // Close sidebar when clicking outside
        document.addEventListener("click", (e) => {
            if (
                window.innerWidth <= 768 &&
                sidebar.classList.contains("active") &&
                !sidebar.contains(e.target) &&
                e.target !== mobileMenuToggle
            ) {
                sidebar.classList.remove("active")
            }
        })
    }

    // Confirm delete
    const deleteButtons = document.querySelectorAll(".btn-xoa")

    deleteButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            if (!confirm("Bạn có chắc chắn muốn xóa?")) {
                e.preventDefault()
            }
        })
    })

    // Image preview for file inputs
    const fileInputs = document.querySelectorAll('input[type="file"]')

    fileInputs.forEach((input) => {
        input.addEventListener("change", function () {
            const preview = document.querySelector("#" + this.dataset.preview)

            if (preview && this.files && this.files[0]) {
                const reader = new FileReader()

                reader.onload = (e) => {
                    preview.src = e.target.result
                    preview.style.display = "block"
                }

                reader.readAsDataURL(this.files[0])
            }
        })
    })

    // Format currency inputs
    const currencyInputs = document.querySelectorAll(".currency-input")

    currencyInputs.forEach((input) => {
        input.addEventListener("input", function () {
            let value = this.value.replace(/\D/g, "")
            if (value) {
                value = Number.parseInt(value, 10).toLocaleString("vi-VN")
                this.value = value
            }
        })
    })

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

