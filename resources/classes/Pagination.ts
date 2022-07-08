class Pagination {
    private readonly pageObject: any;

    constructor(pageObject: any) {
        this.pageObject = pageObject;
    }

    currentPage(): number {
        if (this.pageObject && this.pageObject.current_page) {
            return this.pageObject.current_page;
        }
        return 0;
    }

    lastPage(): number {
        if (this.pageObject && this.pageObject.last_page) {
            return this.pageObject.last_page;
        }
        return 0;
    }

    onFirstPage(): boolean {
        return this.currentPage() == 1;
    }

    onLastPage(): boolean {
        return this.currentPage() == this.lastPage();
    }

    previousPage(): number {
        return this.onFirstPage() ? 1 : this.currentPage() - 1;
    }

    nextPage(): number {
        return this.onLastPage() ? this.lastPage() : this.currentPage() + 1;
    }

    getCurrentPageData(): Array<any> {
        if (this.pageObject && this.pageObject.data) {
            return this.pageObject.data;
        }
        return [];
    }
}

export default Pagination;
