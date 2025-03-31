"use client"

import { useState } from "react"
import {
  AlertCircle,
  Calendar,
  Check,
  ChevronDown,
  ChevronLeft,
  ChevronRight,
  Clock,
  FileText,
  Filter,
  GraduationCap,
  Menu,
  User,
  X,
  MapPin,
} from "lucide-react"
import Image from "next/image"
import Link from "next/link"

import { Button } from "@/components/ui/button"
import { Calendar as CalendarComponent } from "@/components/ui/calendar"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog"
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Textarea } from "@/components/ui/textarea"

export default function AttendancePage() {
  // Mock data - in a real app, this would come from an API or database
  const studentName = "Alex Johnson"
  const currentTerm = "Spring 2025"

  const courses = [
    { id: "CS101", name: "Introduction to Computer Science", instructor: "Dr. Robert Chen" },
    { id: "MATH201", name: "Calculus II", instructor: "Dr. Maria Garcia" },
    { id: "PHYS150", name: "Physics I", instructor: "Dr. James Wilson" },
    { id: "ENG102", name: "Academic Writing", instructor: "Prof. Sarah Miller" },
  ]

  const attendanceRecords = [
    { id: 1, courseId: "CS101", date: "2025-04-01", status: "present", time: "09:00 AM - 10:30 AM" },
    { id: 2, courseId: "CS101", date: "2025-04-03", status: "present", time: "09:00 AM - 10:30 AM" },
    { id: 3, courseId: "CS101", date: "2025-04-08", status: "absent", time: "09:00 AM - 10:30 AM" },
    { id: 4, courseId: "CS101", date: "2025-04-10", status: "present", time: "09:00 AM - 10:30 AM" },
    { id: 5, courseId: "CS101", date: "2025-04-15", status: "present", time: "09:00 AM - 10:30 AM" },
    { id: 6, courseId: "MATH201", date: "2025-04-02", status: "present", time: "11:00 AM - 12:30 PM" },
    { id: 7, courseId: "MATH201", date: "2025-04-04", status: "late", time: "11:00 AM - 12:30 PM" },
    { id: 8, courseId: "MATH201", date: "2025-04-09", status: "present", time: "11:00 AM - 12:30 PM" },
    { id: 9, courseId: "MATH201", date: "2025-04-11", status: "absent", time: "11:00 AM - 12:30 PM" },
    { id: 10, courseId: "MATH201", date: "2025-04-16", status: "present", time: "11:00 AM - 12:30 PM" },
    { id: 11, courseId: "PHYS150", date: "2025-04-02", status: "present", time: "02:00 PM - 03:30 PM" },
    { id: 12, courseId: "PHYS150", date: "2025-04-04", status: "present", time: "02:00 PM - 03:30 PM" },
    { id: 13, courseId: "PHYS150", date: "2025-04-09", status: "present", time: "02:00 PM - 03:30 PM" },
    { id: 14, courseId: "PHYS150", date: "2025-04-11", status: "late", time: "02:00 PM - 03:30 PM" },
    { id: 15, courseId: "PHYS150", date: "2025-04-16", status: "present", time: "02:00 PM - 03:30 PM" },
    { id: 16, courseId: "ENG102", date: "2025-04-01", status: "present", time: "03:30 PM - 05:00 PM" },
    { id: 17, courseId: "ENG102", date: "2025-04-03", status: "absent", time: "03:30 PM - 05:00 PM" },
    { id: 18, courseId: "ENG102", date: "2025-04-08", status: "present", time: "03:30 PM - 05:00 PM" },
    { id: 19, courseId: "ENG102", date: "2025-04-10", status: "present", time: "03:30 PM - 05:00 PM" },
    { id: 20, courseId: "ENG102", date: "2025-04-15", status: "present", time: "03:30 PM - 05:00 PM" },
  ]

  const upcomingClasses = [
    { id: 1, courseId: "CS101", date: "2025-04-22", time: "09:00 AM - 10:30 AM", room: "Tech Building 101" },
    { id: 2, courseId: "MATH201", date: "2025-04-23", time: "11:00 AM - 12:30 PM", room: "Science Hall 205" },
    { id: 3, courseId: "PHYS150", date: "2025-04-23", time: "02:00 PM - 03:30 PM", room: "Science Hall 110" },
    { id: 4, courseId: "ENG102", date: "2025-04-22", time: "03:30 PM - 05:00 PM", room: "Humanities 305" },
  ]

  // State for filters
  const [selectedCourse, setSelectedCourse] = useState<string>("all")
  const [dateRange, setDateRange] = useState<{ from: Date | undefined; to: Date | undefined }>({
    from: undefined,
    to: undefined,
  })
  const [statusFilter, setStatusFilter] = useState<{
    present: boolean
    absent: boolean
    late: boolean
  }>({
    present: true,
    absent: true,
    late: true,
  })

  // Filter attendance records based on selected filters
  const filteredAttendance = attendanceRecords.filter((record) => {
    // Filter by course
    if (selectedCourse !== "all" && record.courseId !== selectedCourse) {
      return false
    }

    // Filter by date range
    if (dateRange.from && new Date(record.date) < dateRange.from) {
      return false
    }
    if (dateRange.to && new Date(record.date) > dateRange.to) {
      return false
    }

    // Filter by status
    if (
      (record.status === "present" && !statusFilter.present) ||
      (record.status === "absent" && !statusFilter.absent) ||
      (record.status === "late" && !statusFilter.late)
    ) {
      return false
    }

    return true
  })

  // Calculate attendance statistics
  const calculateStats = (courseId = "all") => {
    const relevantRecords =
      courseId === "all" ? attendanceRecords : attendanceRecords.filter((record) => record.courseId === courseId)

    const total = relevantRecords.length
    const present = relevantRecords.filter((record) => record.status === "present").length
    const absent = relevantRecords.filter((record) => record.status === "absent").length
    const late = relevantRecords.filter((record) => record.status === "late").length

    const presentPercentage = total > 0 ? (present / total) * 100 : 0
    const absentPercentage = total > 0 ? (absent / total) * 100 : 0
    const latePercentage = total > 0 ? (late / total) * 100 : 0

    return {
      total,
      present,
      absent,
      late,
      presentPercentage,
      absentPercentage,
      latePercentage,
    }
  }

  const stats = calculateStats(selectedCourse === "all" ? "all" : selectedCourse)

  // Function to get course name by ID
  const getCourseNameById = (courseId: string) => {
    const course = courses.find((c) => c.courseId === courseId)
    return course ? course.name : courseId
  }

  // Function to format date
  const formatDate = (dateString: string) => {
    const date = new Date(dateString)
    return date.toLocaleDateString("en-US", {
      weekday: "short",
      month: "short",
      day: "numeric",
    })
  }

  // Function to get status badge class
  const getStatusBadgeClass = (status: string) => {
    switch (status) {
      case "present":
        return "bg-green-100 text-green-800"
      case "absent":
        return "bg-red-100 text-red-800"
      case "late":
        return "bg-yellow-100 text-yellow-800"
      default:
        return "bg-gray-100 text-gray-800"
    }
  }

  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      {/* Navigation - Mobile */}
      <div className="md:hidden flex items-center justify-between p-4 bg-white border-b">
        <div className="flex items-center gap-2">
          <Image
            src="/placeholder.svg?height=40&width=40"
            alt="University Logo"
            width={40}
            height={40}
            className="rounded"
          />
          <span className="font-semibold text-lg">Student Portal</span>
        </div>
        <Sheet>
          <SheetTrigger asChild>
            <Button variant="ghost" size="icon">
              <Menu className="h-6 w-6" />
              <span className="sr-only">Open menu</span>
            </Button>
          </SheetTrigger>
          <SheetContent side="right">
            <nav className="flex flex-col gap-4 mt-8">
              <Link href="/" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <GraduationCap className="h-5 w-5" />
                <span>Dashboard</span>
              </Link>
              <Link href="/profile" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <User className="h-5 w-5" />
                <span>Profile</span>
              </Link>
              <Link href="/attendance" className="flex items-center gap-2 p-2 bg-gray-100 rounded-md">
                <Clock className="h-5 w-5" />
                <span>Attendance</span>
              </Link>
              <Link href="#" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <FileText className="h-5 w-5" />
                <span>Courses</span>
              </Link>
            </nav>
          </SheetContent>
        </Sheet>
      </div>

      {/* Navigation - Desktop */}
      <div className="hidden md:flex items-center justify-between p-4 bg-white border-b">
        <div className="flex items-center gap-3">
          <Image
            src="/placeholder.svg?height=48&width=48"
            alt="University Logo"
            width={48}
            height={48}
            className="rounded"
          />
          <span className="font-semibold text-xl">Student Portal</span>
        </div>
        <nav className="flex items-center gap-6">
          <Link href="/" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <GraduationCap className="h-4 w-4" />
            <span>Dashboard</span>
          </Link>
          <Link href="/profile" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <User className="h-4 w-4" />
            <span>Profile</span>
          </Link>
          <Link href="/attendance" className="text-primary font-medium flex items-center gap-1">
            <Clock className="h-4 w-4" />
            <span>Attendance</span>
          </Link>
          <Link href="#" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <FileText className="h-4 w-4" />
            <span>Courses</span>
          </Link>
        </nav>
      </div>

      {/* Main Content */}
      <main className="flex-1 p-4 md:p-8">
        <div className="max-w-7xl mx-auto space-y-8">
          {/* Header */}
          <div>
            <h1 className="text-2xl md:text-3xl font-bold">Attendance Records</h1>
            <p className="text-gray-500 mt-1">
              {currentTerm} • {studentName}
            </p>
          </div>

          {/* Attendance Summary Cards */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-base">Total Classes</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-3xl font-bold">{stats.total}</div>
                <p className="text-sm text-gray-500">Scheduled sessions</p>
              </CardContent>
            </Card>
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-base">Present</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="flex items-end gap-2">
                  <div className="text-3xl font-bold text-green-600">{stats.present}</div>
                  <div className="text-sm text-gray-500 mb-1">({stats.presentPercentage.toFixed(1)}%)</div>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
                  <div className="bg-green-500 h-2 rounded-full" style={{ width: `${stats.presentPercentage}%` }}></div>
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-base">Absent</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="flex items-end gap-2">
                  <div className="text-3xl font-bold text-red-600">{stats.absent}</div>
                  <div className="text-sm text-gray-500 mb-1">({stats.absentPercentage.toFixed(1)}%)</div>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
                  <div className="bg-red-500 h-2 rounded-full" style={{ width: `${stats.absentPercentage}%` }}></div>
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-base">Late</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="flex items-end gap-2">
                  <div className="text-3xl font-bold text-yellow-600">{stats.late}</div>
                  <div className="text-sm text-gray-500 mb-1">({stats.latePercentage.toFixed(1)}%)</div>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
                  <div className="bg-yellow-500 h-2 rounded-full" style={{ width: `${stats.latePercentage}%` }}></div>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Tabs for different views */}
          <Tabs defaultValue="records">
            <TabsList className="grid grid-cols-3 md:w-[400px]">
              <TabsTrigger value="records">Records</TabsTrigger>
              <TabsTrigger value="calendar">Calendar</TabsTrigger>
              <TabsTrigger value="upcoming">Upcoming</TabsTrigger>
            </TabsList>

            {/* Records Tab */}
            <TabsContent value="records" className="mt-6 space-y-6">
              {/* Filters */}
              <div className="flex flex-col md:flex-row gap-4 md:items-end">
                <div className="space-y-2 flex-1">
                  <Label htmlFor="course-filter">Course</Label>
                  <Select value={selectedCourse} onValueChange={setSelectedCourse}>
                    <SelectTrigger id="course-filter">
                      <SelectValue placeholder="Select Course" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">All Courses</SelectItem>
                      {courses.map((course) => (
                        <SelectItem key={course.id} value={course.id}>
                          {course.id} - {course.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <Label>Date Range</Label>
                  <Popover>
                    <PopoverTrigger asChild>
                      <Button variant="outline" className="w-full justify-start text-left font-normal md:w-[300px]">
                        <Calendar className="mr-2 h-4 w-4" />
                        {dateRange.from ? (
                          dateRange.to ? (
                            <>
                              {dateRange.from.toLocaleDateString()} - {dateRange.to.toLocaleDateString()}
                            </>
                          ) : (
                            dateRange.from.toLocaleDateString()
                          )
                        ) : (
                          <span>Pick a date range</span>
                        )}
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0" align="start">
                      <CalendarComponent
                        initialFocus
                        mode="range"
                        defaultMonth={new Date()}
                        selected={dateRange}
                        onSelect={setDateRange}
                        numberOfMonths={2}
                      />
                      <div className="flex items-center justify-between p-3 border-t">
                        <Button variant="ghost" onClick={() => setDateRange({ from: undefined, to: undefined })}>
                          Clear
                        </Button>
                        <Button onClick={() => document.body.click()}>Apply</Button>
                      </div>
                    </PopoverContent>
                  </Popover>
                </div>

                <div>
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="outline" className="gap-2">
                        <Filter className="h-4 w-4" />
                        Status
                        <ChevronDown className="h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuLabel>Filter by Status</DropdownMenuLabel>
                      <DropdownMenuSeparator />
                      <DropdownMenuCheckboxItem
                        checked={statusFilter.present}
                        onCheckedChange={(checked) => setStatusFilter((prev) => ({ ...prev, present: !!checked }))}
                      >
                        Present
                      </DropdownMenuCheckboxItem>
                      <DropdownMenuCheckboxItem
                        checked={statusFilter.absent}
                        onCheckedChange={(checked) => setStatusFilter((prev) => ({ ...prev, absent: !!checked }))}
                      >
                        Absent
                      </DropdownMenuCheckboxItem>
                      <DropdownMenuCheckboxItem
                        checked={statusFilter.late}
                        onCheckedChange={(checked) => setStatusFilter((prev) => ({ ...prev, late: !!checked }))}
                      >
                        Late
                      </DropdownMenuCheckboxItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </div>
              </div>

              {/* Attendance Records Table */}
              <Card>
                <CardHeader>
                  <CardTitle>Attendance Records</CardTitle>
                  <CardDescription>
                    Showing {filteredAttendance.length} of {attendanceRecords.length} records
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="rounded-md border">
                    <Table>
                      <TableHeader>
                        <TableRow>
                          <TableHead>Date</TableHead>
                          <TableHead>Course</TableHead>
                          <TableHead>Time</TableHead>
                          <TableHead>Status</TableHead>
                          <TableHead className="text-right">Actions</TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        {filteredAttendance.length > 0 ? (
                          filteredAttendance.map((record) => {
                            const course = courses.find((c) => c.id === record.courseId)
                            return (
                              <TableRow key={record.id}>
                                <TableCell>{formatDate(record.date)}</TableCell>
                                <TableCell>
                                  <div className="font-medium">{record.courseId}</div>
                                  <div className="text-sm text-gray-500">{course?.name}</div>
                                </TableCell>
                                <TableCell>{record.time}</TableCell>
                                <TableCell>
                                  <span
                                    className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadgeClass(record.status)}`}
                                  >
                                    {record.status.charAt(0).toUpperCase() + record.status.slice(1)}
                                  </span>
                                </TableCell>
                                <TableCell className="text-right">
                                  <Dialog>
                                    <DialogTrigger asChild>
                                      <Button variant="ghost" size="sm">
                                        Request Correction
                                      </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                      <DialogHeader>
                                        <DialogTitle>Request Attendance Correction</DialogTitle>
                                        <DialogDescription>
                                          Submit a request to correct your attendance record for {course?.name} on{" "}
                                          {formatDate(record.date)}.
                                        </DialogDescription>
                                      </DialogHeader>
                                      <div className="space-y-4 py-4">
                                        <div className="space-y-2">
                                          <Label htmlFor="correction-type">Correction Type</Label>
                                          <Select defaultValue="present">
                                            <SelectTrigger id="correction-type">
                                              <SelectValue placeholder="Select type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                              <SelectItem value="present">Mark as Present</SelectItem>
                                              <SelectItem value="excused">Mark as Excused Absence</SelectItem>
                                              <SelectItem value="late">Mark as Late</SelectItem>
                                            </SelectContent>
                                          </Select>
                                        </div>
                                        <div className="space-y-2">
                                          <Label htmlFor="reason">Reason for Correction</Label>
                                          <Textarea
                                            id="reason"
                                            placeholder="Please provide details about why this correction is needed..."
                                            rows={4}
                                          />
                                        </div>
                                        <div className="space-y-2">
                                          <Label htmlFor="evidence">Supporting Evidence (Optional)</Label>
                                          <Input id="evidence" type="file" />
                                          <p className="text-xs text-gray-500 mt-1">
                                            Upload any supporting documents (e.g., doctor's note, email confirmation)
                                          </p>
                                        </div>
                                      </div>
                                      <DialogFooter>
                                        <Button variant="outline">Cancel</Button>
                                        <Button>Submit Request</Button>
                                      </DialogFooter>
                                    </DialogContent>
                                  </Dialog>
                                </TableCell>
                              </TableRow>
                            )
                          })
                        ) : (
                          <TableRow>
                            <TableCell colSpan={5} className="text-center py-8 text-gray-500">
                              No attendance records found matching your filters
                            </TableCell>
                          </TableRow>
                        )}
                      </TableBody>
                    </Table>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            {/* Calendar Tab */}
            <TabsContent value="calendar" className="mt-6">
              <Card>
                <CardHeader>
                  <CardTitle>Attendance Calendar</CardTitle>
                  <CardDescription>View your attendance by date</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="flex flex-col md:flex-row gap-6">
                    <div className="md:w-[350px]">
                      <div className="p-4 border rounded-lg">
                        <div className="flex items-center justify-between mb-4">
                          <Button variant="outline" size="icon">
                            <ChevronLeft className="h-4 w-4" />
                          </Button>
                          <h3 className="font-medium">April 2025</h3>
                          <Button variant="outline" size="icon">
                            <ChevronRight className="h-4 w-4" />
                          </Button>
                        </div>
                        <div className="grid grid-cols-7 gap-1 text-center text-sm mb-2">
                          <div className="text-gray-500">Su</div>
                          <div className="text-gray-500">Mo</div>
                          <div className="text-gray-500">Tu</div>
                          <div className="text-gray-500">We</div>
                          <div className="text-gray-500">Th</div>
                          <div className="text-gray-500">Fr</div>
                          <div className="text-gray-500">Sa</div>
                        </div>
                        <div className="grid grid-cols-7 gap-1 text-center">
                          {/* Empty cells for days before the month starts */}
                          <div></div>
                          {Array.from({ length: 30 }, (_, i) => {
                            const day = i + 1
                            const dateStr = `2025-04-${day.toString().padStart(2, "0")}`
                            const records = attendanceRecords.filter((r) => r.date === dateStr)

                            let statusClass = ""
                            if (records.length > 0) {
                              if (records.some((r) => r.status === "absent")) {
                                statusClass = "bg-red-100 text-red-800"
                              } else if (records.some((r) => r.status === "late")) {
                                statusClass = "bg-yellow-100 text-yellow-800"
                              } else {
                                statusClass = "bg-green-100 text-green-800"
                              }
                            }

                            return (
                              <div
                                key={day}
                                className={`aspect-square flex items-center justify-center rounded-full cursor-pointer hover:bg-gray-100 ${statusClass} ${day === 20 ? "ring-2 ring-primary" : ""}`}
                              >
                                {day}
                              </div>
                            )
                          })}
                        </div>
                        <div className="flex justify-center gap-6 mt-6">
                          <div className="flex items-center gap-2">
                            <div className="w-3 h-3 rounded-full bg-green-500"></div>
                            <span className="text-sm">Present</span>
                          </div>
                          <div className="flex items-center gap-2">
                            <div className="w-3 h-3 rounded-full bg-red-500"></div>
                            <span className="text-sm">Absent</span>
                          </div>
                          <div className="flex items-center gap-2">
                            <div className="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <span className="text-sm">Late</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div className="flex-1">
                      <div className="border rounded-lg p-6">
                        <h3 className="text-lg font-medium mb-4">April 20, 2025</h3>
                        <div className="space-y-4">
                          {attendanceRecords
                            .filter((record) => record.date === "2025-04-10")
                            .map((record) => {
                              const course = courses.find((c) => c.id === record.courseId)
                              return (
                                <div key={record.id} className="flex items-start gap-4 p-4 border rounded-lg">
                                  <div
                                    className={`p-2 rounded-full ${
                                      record.status === "present"
                                        ? "bg-green-100"
                                        : record.status === "absent"
                                          ? "bg-red-100"
                                          : "bg-yellow-100"
                                    }`}
                                  >
                                    {record.status === "present" ? (
                                      <Check
                                        className={`h-5 w-5 ${record.status === "present" ? "text-green-600" : "text-red-600"}`}
                                      />
                                    ) : record.status === "absent" ? (
                                      <X className="h-5 w-5 text-red-600" />
                                    ) : (
                                      <Clock className="h-5 w-5 text-yellow-600" />
                                    )}
                                  </div>
                                  <div className="flex-1">
                                    <div className="font-medium">{course?.name}</div>
                                    <div className="text-sm text-gray-500">{record.time}</div>
                                    <div className="mt-2">
                                      <span
                                        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadgeClass(record.status)}`}
                                      >
                                        {record.status.charAt(0).toUpperCase() + record.status.slice(1)}
                                      </span>
                                    </div>
                                  </div>
                                </div>
                              )
                            })}
                          {attendanceRecords.filter((record) => record.date === "2025-04-10").length === 0 && (
                            <div className="text-center py-8 text-gray-500">No attendance records for this date</div>
                          )}
                        </div>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            {/* Upcoming Tab */}
            <TabsContent value="upcoming" className="mt-6">
              <Card>
                <CardHeader>
                  <CardTitle>Upcoming Classes</CardTitle>
                  <CardDescription>Classes scheduled for the next week</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {upcomingClasses.map((classItem) => {
                      const course = courses.find((c) => c.id === classItem.courseId)
                      return (
                        <div key={classItem.id} className="flex items-start gap-4 p-4 border rounded-lg">
                          <div className="bg-primary/10 rounded-md p-2 text-primary">
                            <Calendar className="h-5 w-5" />
                          </div>
                          <div className="flex-1">
                            <div className="font-medium">{course?.name}</div>
                            <div className="text-sm text-gray-500">
                              {formatDate(classItem.date)} • {classItem.time}
                            </div>
                            <div className="flex items-center gap-2 mt-1 text-sm">
                              <MapPin className="h-4 w-4 text-gray-500" />
                              <span>{classItem.room}</span>
                            </div>
                          </div>
                          <Button variant="outline" size="sm">
                            Set Reminder
                          </Button>
                        </div>
                      )
                    })}
                  </div>
                </CardContent>
                <CardFooter className="flex justify-between">
                  <div className="flex items-center gap-2 text-sm text-gray-500">
                    <AlertCircle className="h-4 w-4" />
                    <span>Attendance is mandatory for all classes</span>
                  </div>
                  <Button variant="outline" size="sm">
                    View Full Schedule
                  </Button>
                </CardFooter>
              </Card>

              <Card className="mt-6">
                <CardHeader>
                  <CardTitle>Attendance Policy</CardTitle>
                  <CardDescription>University attendance requirements</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4 text-sm">
                    <p>
                      Students are expected to attend all scheduled classes and laboratory sessions. Attendance is taken
                      at the beginning of each class.
                    </p>
                    <div className="space-y-2">
                      <h4 className="font-medium">Absence Policy:</h4>
                      <ul className="list-disc pl-5 space-y-1">
                        <li>Students are allowed a maximum of 3 absences per course per semester.</li>
                        <li>Exceeding this limit may result in grade penalties or course failure.</li>
                        <li>Medical absences require documentation within 7 days of return.</li>
                      </ul>
                    </div>
                    <div className="space-y-2">
                      <h4 className="font-medium">Tardiness:</h4>
                      <ul className="list-disc pl-5 space-y-1">
                        <li>Arriving more than 10 minutes late is considered "late".</li>
                        <li>Three "late" marks will count as one absence.</li>
                      </ul>
                    </div>
                    <div className="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                      <div className="flex items-start gap-3">
                        <AlertCircle className="h-5 w-5 text-yellow-600 mt-0.5" />
                        <div>
                          <h4 className="font-medium text-yellow-800">Important Notice</h4>
                          <p className="text-yellow-700">
                            Students with attendance below 75% will not be eligible for final examinations.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </CardContent>
                <CardFooter>
                  <Button variant="outline" className="w-full">
                    Download Complete Attendance Policy
                  </Button>
                </CardFooter>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </main>

      {/* Footer */}
      <footer className="bg-white border-t p-6 md:p-8 mt-8">
        <div className="max-w-7xl mx-auto">
          <div className="grid md:grid-cols-3 gap-8">
            <div>
              <h3 className="font-semibold mb-3">Contact Information</h3>
              <address className="not-italic text-sm text-gray-600 space-y-1">
                <p>University Student Services</p>
                <p>123 Campus Drive</p>
                <p>Email: support@university.edu</p>
                <p>Phone: (555) 123-4567</p>
              </address>
            </div>
            <div>
              <h3 className="font-semibold mb-3">Quick Links</h3>
              <ul className="text-sm space-y-2">
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    Academic Calendar
                  </Link>
                </li>
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    Student Handbook
                  </Link>
                </li>
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    Library Resources
                  </Link>
                </li>
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    IT Support
                  </Link>
                </li>
              </ul>
            </div>
            <div>
              <h3 className="font-semibold mb-3">Connect With Us</h3>
              <div className="flex gap-4">
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                  </svg>
                  <span className="sr-only">Facebook</span>
                </Link>
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                  </svg>
                  <span className="sr-only">Instagram</span>
                </Link>
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                  </svg>
                  <span className="sr-only">Twitter</span>
                </Link>
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                    <rect x="2" y="9" width="4" height="12"></rect>
                    <circle cx="4" cy="4" r="2"></circle>
                  </svg>
                  <span className="sr-only">LinkedIn</span>
                </Link>
              </div>
              <div className="mt-4 text-sm text-gray-600">
                <p>© 2025 University Name. All rights reserved.</p>
                <div className="mt-2 flex gap-4">
                  <Link href="#" className="hover:text-primary">
                    Privacy Policy
                  </Link>
                  <Link href="#" className="hover:text-primary">
                    Terms of Use
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}

